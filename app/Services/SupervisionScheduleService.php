<?php

namespace App\Services;

use App\Enums\SupervisionStatus;
use App\Models\SupervisionRequest;
use App\Models\SupervisionSlot;
use App\Models\Thesis;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SupervisionScheduleService
{
    /**
     * Create a lecturer availability slot.
     */
    public function createSlot(User $supervisor, array $data): SupervisionSlot
    {
        $date = Carbon::parse($data['date']);
        $startTime = $data['start_time'];
        $endTime = $data['end_time'];

        if ($startTime >= $endTime) {
            throw ValidationException::withMessages([
                'end_time' => 'Waktu selesai harus lebih lambat dari waktu mulai.',
            ]);
        }

        // Check overlapping slot for same supervisor
        $conflict = SupervisionSlot::where('supervisor_id', $supervisor->id)
            ->where('date', $date->toDateString())
            ->where('is_active', true)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                      ->orWhereBetween('end_time', [$startTime, $endTime])
                      ->orWhere(function ($q) use ($startTime, $endTime) {
                          $q->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                      });
            })->exists();

        if ($conflict) {
            throw ValidationException::withMessages([
                'start_time' => 'Anda sudah memiliki jadwal ketersediaan lain pada rentang waktu yang sama.',
            ]);
        }

        return SupervisionSlot::create([
            'supervisor_id' => $supervisor->id,
            'date' => $date->toDateString(),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'quota' => $data['quota'] ?? 1,
            'booked_count' => 0,
            'location_type' => $data['location_type'] ?? 'offline',
            'room_or_link' => $data['room_or_link'] ?? null,
            'is_active' => true,
        ]);
    }

    /**
     * Book an available supervision slot.
     */
    public function bookSlot(User $student, SupervisionSlot $slot, array $data): SupervisionRequest
    {
        $thesis = $student->studentThesis;
        if (!$thesis) {
            throw ValidationException::withMessages([
                'slot_id' => 'Mahasiswa belum memiliki data tugas akhir / skripsi aktif.',
            ]);
        }

        // Check if supervisor matches thesis pembimbing 1 or 2
        if (!$thesis->isSupervisor($slot->supervisor_id)) {
            throw ValidationException::withMessages([
                'slot_id' => 'Dosen tersebut bukan merupakan pembimbing Anda.',
            ]);
        }

        $slotEnd = Carbon::parse($slot->date->toDateString() . ' ' . $slot->end_time);
        if ($slotEnd->isPast()) {
            throw ValidationException::withMessages([
                'slot_id' => 'Slot bimbingan ini sudah berlalu.',
            ]);
        }

        return DB::transaction(function () use ($student, $slot, $thesis, $data) {
            $lockedSlot = SupervisionSlot::where('id', $slot->id)->lockForUpdate()->firstOrFail();

            if (!$lockedSlot->is_active) {
                throw ValidationException::withMessages([
                    'slot_id' => 'Slot bimbingan ini sudah tidak aktif.',
                ]);
            }

            if (!$lockedSlot->hasAvailableQuota()) {
                throw ValidationException::withMessages([
                    'slot_id' => 'Kuota bimbingan pada slot ini sudah penuh.',
                ]);
            }

            $existing = SupervisionRequest::where('student_id', $student->id)
                ->where('supervision_slot_id', $lockedSlot->id)
                ->whereIn('status', [SupervisionStatus::REQUESTED, SupervisionStatus::ACCEPTED])
                ->exists();

            if ($existing) {
                throw ValidationException::withMessages([
                    'slot_id' => 'Anda sudah mengajukan bimbingan pada slot ini.',
                ]);
            }

            $request = SupervisionRequest::create([
                'supervision_slot_id' => $lockedSlot->id,
                'thesis_id' => $thesis->id,
                'student_id' => $student->id,
                'supervisor_id' => $lockedSlot->supervisor_id,
                'topic' => $data['topic'],
                'student_notes' => $data['student_notes'] ?? null,
                'status' => SupervisionStatus::REQUESTED,
                'scheduled_at' => Carbon::parse($lockedSlot->date)->format('Y-m-d') . ' ' . $lockedSlot->start_time,
            ]);

            $lockedSlot->increment('booked_count');

            return $request;
        });
    }

    /**
     * Update request status by supervisor.
     */
    public function updateStatus(SupervisionRequest $request, SupervisionStatus $status, ?string $notes = null): SupervisionRequest
    {
        return DB::transaction(function () use ($request, $status, $notes) {
            $previousStatus = $request->status;

            $allowedTransitions = [
                SupervisionStatus::REQUESTED->value => [
                    SupervisionStatus::ACCEPTED->value,
                    SupervisionStatus::REJECTED->value,
                    SupervisionStatus::RESCHEDULED->value,
                ],
                SupervisionStatus::RESCHEDULED->value => [
                    SupervisionStatus::ACCEPTED->value,
                    SupervisionStatus::REJECTED->value,
                    SupervisionStatus::RESCHEDULED->value,
                ],
                SupervisionStatus::ACCEPTED->value => [
                    SupervisionStatus::RESCHEDULED->value,
                    SupervisionStatus::REJECTED->value,
                ],
            ];

            if (!in_array($status->value, $allowedTransitions[$previousStatus->value] ?? [], true)) {
                throw ValidationException::withMessages([
                    'status' => 'Perubahan status bimbingan tidak valid dari status saat ini.',
                ]);
            }

            $request->update([
                'status' => $status,
                'supervisor_notes' => $notes ?? $request->supervisor_notes,
            ]);

            // If rejected or cancelled from a non-rejected/cancelled state, decrement slot booked_count safely
            if (
                in_array($status, [SupervisionStatus::REJECTED, SupervisionStatus::CANCELLED])
                && !in_array($previousStatus, [SupervisionStatus::REJECTED, SupervisionStatus::CANCELLED])
                && $request->supervision_slot_id
            ) {
                $lockedSlot = SupervisionSlot::where('id', $request->supervision_slot_id)->lockForUpdate()->first();
                if ($lockedSlot && $lockedSlot->booked_count > 0) {
                    $lockedSlot->decrement('booked_count');
                }
            }

            return $request;
        });
    }

    /**
     * Complete supervision session with notes.
     */
    public function completeSession(SupervisionRequest $request, array $data): SupervisionRequest
    {
        return DB::transaction(function () use ($request, $data) {
            if ($request->status !== SupervisionStatus::ACCEPTED) {
                throw ValidationException::withMessages([
                    'status' => 'Hanya sesi bimbingan yang sudah diterima yang dapat diselesaikan.',
                ]);
            }

            $request->update([
                'status' => SupervisionStatus::COMPLETED,
                'meeting_summary' => $data['meeting_summary'],
                'action_items' => $data['action_items'] ?? null,
                'completed_at' => now(),
            ]);

            return $request->fresh();
        });
    }
}
