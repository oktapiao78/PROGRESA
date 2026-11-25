<?php

namespace App\Http\Controllers;

use App\Enums\ApprovalStatus;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\ThesisChapter;
use App\Services\ApprovalWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    protected ApprovalWorkflowService $workflowService;

    public function __construct(ApprovalWorkflowService $workflowService)
    {
        $this->workflowService = $workflowService;
    }

    public function approveStage(Request $request, ThesisChapter $chapter): RedirectResponse
    {
        $validated = $request->validate([
            'stage' => ['required', 'string', 'in:pembimbing_1,pembimbing_2'],
            'status' => ['required', 'string', 'in:approved,revision_requested,rejected'],
            'feedback' => ['nullable', 'string', 'max:2000'],
        ]);

        $statusEnum = ApprovalStatus::from($validated['status']);

        try {
            $approval = $this->workflowService->processApproval(
                $chapter,
                auth()->user(),
                $validated['stage'],
                $statusEnum,
                $validated['feedback'] ?? null
            );

            Notification::sendTo(
                $chapter->thesis->student_id,
                'Hasil Review ' . $chapter->title . ': ' . $statusEnum->label(),
                auth()->user()->name . " memberikan hasil review ({$statusEnum->label()}) pada {$chapter->title}.",
                'chapter_review',
                route('chapters.show', $chapter)
            );

            return back()->with('success', "Persetujuan tahap {$approval->stageLabel()} berhasil diperbarui ({$statusEnum->label()}).");
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        }
    }

}
