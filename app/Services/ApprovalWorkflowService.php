<?php

namespace App\Services;

use App\Enums\ApprovalStatus;
use App\Enums\ChapterStatus;
use App\Models\AuditLog;
use App\Models\ChapterApproval;
use App\Models\ThesisChapter;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class ApprovalWorkflowService
{
    /**
     * Process approval or revision request for a specific stage.
     */
    public function processApproval(
        ThesisChapter $chapter,
        User $approver,
        string $stage,
        ApprovalStatus $status,
        ?string $feedback = null
    ): ChapterApproval {
        $thesis = $chapter->thesis;

        // Verify approver matches stage
        if ($stage === 'pembimbing_1' && $thesis->supervisor_1_id !== $approver->id) {
            throw ValidationException::withMessages(['stage' => 'Anda bukan Pembimbing 1 untuk mahasiswa ini.']);
        }
        if ($stage === 'pembimbing_2' && $thesis->supervisor_2_id !== $approver->id) {
            throw ValidationException::withMessages(['stage' => 'Anda bukan Pembimbing 2 untuk mahasiswa ini.']);
        }
        if (!in_array($stage, ['pembimbing_1', 'pembimbing_2'], true)) {
            throw ValidationException::withMessages(['stage' => 'Tahap approval hanya tersedia untuk Pembimbing 1 dan Pembimbing 2.']);
        }

        // If approving, ensure no open revision items exist
        if ($status === ApprovalStatus::APPROVED) {
            $openRevisions = $chapter->openRevisions()->count();
            if ($openRevisions > 0) {
                throw ValidationException::withMessages([
                    'status' => "Terdapat {$openRevisions} butir revisi yang belum diselesaikan oleh mahasiswa.",
                ]);
            }
        }

        $approval = DB::transaction(function () use ($chapter, $stage, $approver, $status, $feedback, $thesis) {
            $approval = ChapterApproval::updateOrCreate(
                ['thesis_chapter_id' => $chapter->id, 'stage' => $stage],
                ['approver_id' => $approver->id, 'status' => $status, 'feedback' => $feedback, 'action_date' => now()]
            );

            if ($status === ApprovalStatus::REVISION_REQUESTED || $status === ApprovalStatus::REJECTED) {
                $chapter->update(['status' => ChapterStatus::REVISION_REQUIRED]);
            } elseif ($status === ApprovalStatus::APPROVED) {
                $chapter->update(['status' => $chapter->fresh()->isFullyApproved() ? ChapterStatus::APPROVED : ChapterStatus::IN_REVIEW]);
            }
            $thesis->recalculateProgress();
            return $approval;
        });

        // Log audit
        AuditLog::log("APPROVAL_{$stage}_{$status->value}", $chapter, "Status: {$status->label()}. Catatan: {$feedback}");

        return $approval;
    }
}
