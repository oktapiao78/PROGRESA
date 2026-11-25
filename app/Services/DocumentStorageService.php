<?php

namespace App\Services;

use App\Models\DocumentVersion;
use App\Models\ThesisChapter;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentStorageService
{
    protected DocumentTextExtractor $textExtractor;

    public function __construct(DocumentTextExtractor $textExtractor)
    {
        $this->textExtractor = $textExtractor;
    }

    /**
     * Store draft document and register new version.
     */
    public function storeDraft(ThesisChapter $chapter, UploadedFile $file, User $uploader, ?string $notes = null): DocumentVersion
    {
        $thesis = $chapter->thesis;
        $nextVersion = ($chapter->versions()->max('version_number') ?? 0) + 1;

        $extension = strtolower($file->getClientOriginalExtension());
        $safeFilename = sprintf(
            'draft_v%d_%s.%s',
            $nextVersion,
            Str::random(12),
            $extension
        );

        $folder = "theses/{$thesis->id}/bab_{$chapter->order}";
        $path = $file->storeAs($folder, $safeFilename, 'local');

        // Extract text for diffing
        $textContent = $this->textExtractor->extractText($file->getRealPath(), $extension);

        $version = DocumentVersion::create([
            'thesis_chapter_id' => $chapter->id,
            'version_number' => $nextVersion,
            'file_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType() ?? 'application/octet-stream',
            'file_size' => $file->getSize(),
            'notes' => $notes,
            'text_content' => $textContent,
            'uploader_id' => $uploader->id,
        ]);

        $chapter->update([
            'current_version_id' => $version->id,
            'status' => \App\Enums\ChapterStatus::IN_REVIEW,
        ]);

        return $version;
    }

    /**
     * Authorize and download document.
     */
    public function download(DocumentVersion $version, User $user): \Symfony\Component\HttpFoundation\Response
    {
        $chapter = $version->chapter;
        $thesis = $chapter->thesis;

        // Authorization check: Student, Supervisor 1/2, Koordinator, Kaprodi, Admin
        $isStudent = $thesis->student_id === $user->id;
        $isSupervisor = $thesis->isSupervisor($user);
        $isStaff = in_array($user->role, [
            \App\Enums\UserRole::KOORDINATOR,
            \App\Enums\UserRole::KAPRODI,
            \App\Enums\UserRole::ADMIN
        ]);

        if (!($isStudent || $isSupervisor || $isStaff)) {
            abort(403, 'Anda tidak memiliki izin untuk mengunduh dokumen ini.');
        }

        $fullPath = storage_path('app/' . $version->file_path);
        if (!file_exists($fullPath)) {
            abort(404, 'File dokumen tidak ditemukan di penyimpanan server.');
        }

        return response()->download(
            $fullPath,
            $version->downloadFilename(),
            ['Content-Type' => $version->mime_type]
        );
    }
}
