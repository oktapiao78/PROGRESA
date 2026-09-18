<?php

namespace App\Http\Controllers;

use App\Enums\SupervisionStatus;
use App\Models\AuditLog;
use App\Models\SupervisionRequest;
use App\Models\SupervisionSlot;
use App\Models\Thesis;
use App\Models\User;
use App\Services\SupervisionScheduleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupervisionController extends Controller
{
    protected SupervisionScheduleService $scheduleService;

    public function __construct(SupervisionScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    public function index(): View
    {
        $user = auth()->user();

        if ($user->isMahasiswa()) {
            $thesis = $user->studentThesis;
            $supervisors = collect();
            $availableSlots = collect();

            if ($thesis) {
                $supervisorIds = array_filter([$thesis->supervisor_1_id, $thesis->supervisor_2_id]);
                $supervisors = User::whereIn('id', $supervisorIds)->get();

                $availableSlots = SupervisionSlot::whereIn('supervisor_id', $supervisorIds)
                    ->where('is_active', true)
                    ->where('date', '>=', now()->toDateString())
                    ->with('supervisor')
                    ->orderBy('date')
                    ->orderBy('start_time')
                    ->get();
            }

            $myRequests = SupervisionRequest::where('student_id', $user->id)
                ->with(['supervisor', 'slot'])
                ->latest()
                ->paginate(10);

            return view('supervision.student_index', compact('thesis', 'supervisors', 'availableSlots', 'myRequests'));
        }

        if ($user->isDosen()) {
            $mySlots = SupervisionSlot::where('supervisor_id', $user->id)
                ->where('date', '>=', now()->subDays(7)->toDateString())
                ->orderBy('date', 'desc')
                ->get();

            $pendingRequests = SupervisionRequest::where('supervisor_id', $user->id)
                ->where('status', SupervisionStatus::REQUESTED)
                ->with(['student', 'thesis', 'slot'])
                ->latest()
                ->get();

            $upcomingSessions = SupervisionRequest::where('supervisor_id', $user->id)
                ->where('status', SupervisionStatus::ACCEPTED)
                ->with(['student', 'thesis', 'slot'])
                ->orderBy('scheduled_at')
                ->get();

            $completedSessions = SupervisionRequest::where('supervisor_id', $user->id)
                ->where('status', SupervisionStatus::COMPLETED)
                ->with(['student', 'thesis'])
                ->latest('completed_at')
                ->paginate(10);

            return view('supervision.lecturer_index', compact('mySlots', 'pendingRequests', 'upcomingSessions', 'completedSessions'));
        }

        // Koordinator / Kaprodi / Admin: monitoring all supervisions
        $allRequests = SupervisionRequest::with(['student', 'supervisor', 'thesis'])
            ->latest()
            ->paginate(15);

        return view('supervision.admin_index', compact('allRequests'));
    }

    public function storeSlot(Request $request): RedirectResponse
    {
        $user = auth()->user();
        if (!$user->isDosen() && !$user->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'quota' => ['required', 'integer', 'min:1', 'max:20'],
            'location_type' => ['required', 'in:offline,online'],
            'room_or_link' => ['nullable', 'string', 'max:255'],
        ]);

        $slot = $this->scheduleService->createSlot($user, $validated);

        AuditLog::log('SUPERVISION_SLOT_CREATED', $slot, "Dosen {$user->name} membuka jadwal bimbingan pada tanggal {$slot->date}.");

        return back()->with('success', 'Jadwal ketersediaan bimbingan berhasil dipublikasikan.');
    }

    public function destroySlot(SupervisionSlot $slot): RedirectResponse
    {
        $user = auth()->user();
        if ($slot->supervisor_id !== $user->id && !$user->isAdmin()) {
            abort(403);
        }

        if ($slot->booked_count > 0) {
            return back()->withErrors(['slot' => 'Slot ini tidak dapat dihapus karena sudah dibooking oleh mahasiswa.']);
        }

        $slot->delete();
        return back()->with('success', 'Slot bimbingan berhasil dihapus.');
    }

    public function book(Request $request, SupervisionSlot $slot): RedirectResponse
    {
        $user = auth()->user();
        if (!$user->isMahasiswa()) {
            abort(403);
        }

        $validated = $request->validate([
            'topic' => ['required', 'string', 'max:255'],
            'student_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $supervisionRequest = $this->scheduleService->bookSlot($user, $slot, $validated);

        AuditLog::log('SUPERVISION_REQUESTED', $supervisionRequest, "Mahasiswa {$user->name} mengajukan bimbingan: {$supervisionRequest->topic}.");

        return back()->with('success', 'Pengajuan bimbingan berhasil dikirim. Menunggu persetujuan dosen pembimbing.');
    }

    public function respond(Request $request, SupervisionRequest $supervisionRequest): RedirectResponse
    {
        $user = auth()->user();
        if ($supervisionRequest->supervisor_id !== $user->id && !$user->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => ['required', 'in:accepted,rejected,rescheduled'],
            'supervisor_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $statusEnum = SupervisionStatus::from($validated['status']);
        $this->scheduleService->updateStatus($supervisionRequest, $statusEnum, $validated['supervisor_notes']);

        AuditLog::log('SUPERVISION_STATUS_UPDATED', $supervisionRequest, "Dosen mengubah status bimbingan menjadi {$statusEnum->label()}.");

        return back()->with('success', "Status pengajuan bimbingan berhasil diperbarui menjadi {$statusEnum->label()}.");
    }

    public function complete(Request $request, SupervisionRequest $supervisionRequest): RedirectResponse
    {
        $user = auth()->user();
        if ($supervisionRequest->supervisor_id !== $user->id && !$user->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'meeting_summary' => ['required', 'string', 'max:2000'],
            'action_items' => ['nullable', 'string', 'max:2000'],
        ]);

        $this->scheduleService->completeSession($supervisionRequest, $validated);

        AuditLog::log('SUPERVISION_COMPLETED', $supervisionRequest, "Sesi bimbingan diselesaikan dengan catatan berita acara.");

        return back()->with('success', 'Berita Acara bimbingan berhasil disimpan dan sesi dinyatakan selesai.');
    }
}
