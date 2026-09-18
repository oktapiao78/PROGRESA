<?php

namespace App\Services;

use App\Enums\ApprovalStatus;
use App\Enums\ChapterStatus;
use App\Models\AuditLog;
use App\Models\ChapterApproval;
use App\Models\ThesisChapter;
use App\Models\User;
use Illuminate\Validation\ValidationException;

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
        if ($stage === 'pembimbing_1' && $thesis->supervisor_1_id !== $approver->id && !$approver->isAdmin()) {
            throw ValidationException::withMessages(['stage' => 'Anda bukan Pembimbing 1 untuk mahasiswa ini.']);
        }
        if ($stage === 'pembimbing_2' && $thesis->supervisor_2_id !== $approver->id && !$approver->isAdmin()) {
            throw ValidationException::withMessages(['stage' => 'Anda bukan Pembimbing 2 untuk mahasiswa ini.']);
        }
        if ($stage === 'koordinator' && !$approver->isKoordinator() && !$approver->isAdmin()) {
            throw ValidationException::withMessages(['stage' => 'Hanya Koordinator TA atau Admin yang dapat melakukan approval tahap ini.']);
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

        $approval = ChapterApproval::updateOrCreate(
            [
                'thesis_chapter_id' => $chapter->id,
                'stage' => $stage,
            ],
            [
                'approver_id' => $approver->id,
                'status' => $status,
                'feedback' => $feedback,
                'action_date' => now(),
            ]
        );

        // Update chapter status
        if ($status === ApprovalStatus::REVISION_REQUESTED) {
            $chapter->update(['status' => ChapterStatus::REVISION_REQUIRED]);
        } elseif ($status === ApprovalStatus::APPROVED) {
            if ($chapter->isFullyApproved()) {
                $chapter->update(['status' => ChapterStatus::APPROVED]);
            } else {
                $chapter->update(['status' => ChapterStatus::IN_REVIEW]);
            }
        } elseif ($status === ApprovalStatus::REJECTED) {
            $chapter->update(['status' => ChapterStatus::REVISION_REQUIRED]);
        }

        // Recalculate parent thesis progress
        $thesis->recalculateProgress();

        // Log audit
        AuditLog::log("APPROVAL_{$stage}_{$status->value}", $chapter, "Status: {$status->label()}. Catatan: {$feedback}");

        return $approval;
    }
}
