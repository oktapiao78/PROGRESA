<?php

namespace App\Http\Controllers;

use App\Enums\ChapterStatus;
use App\Models\AuditLog;
use App\Models\DocumentVersion;
use App\Models\ThesisChapter;
use App\Services\AnalyticsAndRiskService;
use App\Services\DocumentStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class ChapterController extends Controller
{
    protected DocumentStorageService $storageService;
    protected AnalyticsAndRiskService $analyticsService;

    public function __construct(DocumentStorageService $storageService, AnalyticsAndRiskService $analyticsService)
    {
        $this->storageService = $storageService;
        $this->analyticsService = $analyticsService;
    }

    public function show(ThesisChapter $chapter): View
    {
        $user = auth()->user();
        $thesis = $chapter->thesis;

        // Verify permission
        $isStudent = $thesis->student_id === $user->id;
        $isSupervisor = $thesis->isSupervisor($user);
        $isStaff = $user->isKoordinator() || $user->isKaprodi() || $user->isAdmin();

        if (!($isStudent || $isSupervisor || $isStaff)) {
            abort(403, 'Anda tidak memiliki hak akses ke bab ini.');
        }

        $chapter->load([
            'thesis.student',
            'thesis.supervisor1',
            'thesis.supervisor2',
            'versions.uploader',
            'currentVersion',
            'revisionItems.supervisor',
            'approvals.approver',
        ]);

        return view('chapters.show', compact('chapter', 'thesis', 'isStudent', 'isSupervisor', 'isStaff'));
    }

    public function uploadDraft(Request $request, ThesisChapter $chapter): RedirectResponse
    {
        $user = auth()->user();
        $thesis = $chapter->thesis;

        if ($thesis->student_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'Hanya mahasiswa pemilik skripsi yang dapat mengunggah draf bab.');
        }

        $request->validate([
            'draft_file' => [
                'required',
                'file',
                'mimes:pdf,docx,doc,odt,txt',
                'max:20480', // 20MB
            ],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'draft_file.required' => 'File draf dokumen wajib diunggah.',
            'draft_file.mimes' => 'Format file harus berupa PDF, DOCX, DOC, ODT, atau TXT.',
            'draft_file.max' => 'Ukuran file draf maksimal 20 MB.',
        ]);

        $version = $this->storageService->storeDraft(
            $chapter,
            $request->file('draft_file'),
            $user,
            $request->input('notes')
        );

        $this->analyticsService->analyzeThesis($thesis);

        AuditLog::log('DRAFT_UPLOADED', $version, "Mahasiswa mengunggah {$chapter->title} Versi {$version->version_number}.");

        return redirect()->route('chapters.show', $chapter)
            ->with('success', "Draf {$chapter->title} versi {$version->version_number} berhasil diunggah.");
    }

    public function downloadVersion(DocumentVersion $version): Response
    {
        return $this->storageService->download($version, auth()->user());
    }
}
