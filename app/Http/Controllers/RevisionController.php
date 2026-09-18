<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\RevisionItem;
use App\Models\ThesisChapter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RevisionController extends Controller
{
    public function store(Request $request, ThesisChapter $chapter): RedirectResponse
    {
        $user = auth()->user();
        $thesis = $chapter->thesis;

        if (!$thesis->isSupervisor($user) && !$user->isAdmin()) {
            abort(403, 'Hanya dosen pembimbing yang dapat menambahkan butir revisi.');
        }

        $validated = $request->validate([
            'note' => ['required', 'string', 'max:2000'],
            'document_version_id' => ['nullable', 'exists:document_versions,id'],
        ]);

        $item = RevisionItem::create([
            'thesis_chapter_id' => $chapter->id,
            'document_version_id' => $validated['document_version_id'] ?? $chapter->current_version_id,
            'supervisor_id' => $user->id,
            'note' => $validated['note'],
            'is_resolved' => false,
        ]);

        $chapter->update(['status' => \App\Enums\ChapterStatus::REVISION_REQUIRED]);

        AuditLog::log('REVISION_ITEM_ADDED', $item, "Dosen {$user->name} menambahkan butir revisi pada {$chapter->title}.");

        return back()->with('success', 'Butir revisi berhasil ditambahkan ke daftar periksa mahasiswa.');
    }

    public function respond(Request $request, RevisionItem $item): RedirectResponse
    {
        $user = auth()->user();
        $thesis = $item->chapter->thesis;

        if ($thesis->student_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'Hanya mahasiswa yang dapat memberikan tanggapan perbaikan revisi.');
        }

        $validated = $request->validate([
            'student_response' => ['required', 'string', 'max:2000'],
        ]);

        $item->update([
            'student_response' => $validated['student_response'],
        ]);

        AuditLog::log('REVISION_RESPONSE_SUBMITTED', $item, "Mahasiswa {$user->name} menanggapi butir revisi.");

        return back()->with('success', 'Tanggapan dan penjelasan perbaikan berhasil disimpan.');
    }

    public function toggleResolve(RevisionItem $item): RedirectResponse
    {
        $user = auth()->user();
        $thesis = $item->chapter->thesis;

        if (!$thesis->isSupervisor($user) && !$user->isAdmin()) {
            abort(403, 'Hanya dosen pembimbing yang dapat memverifikasi status penyelesaian revisi.');
        }

        $newStatus = !$item->is_resolved;
        $item->update([
            'is_resolved' => $newStatus,
            'resolved_at' => $newStatus ? now() : null,
        ]);

        $statusLabel = $newStatus ? 'diselesaikan' : 'dibuka kembali';
        AuditLog::log('REVISION_STATUS_TOGGLED', $item, "Butir revisi {$statusLabel} oleh Dosen {$user->name}.");

        return back()->with('success', "Butir revisi berhasil ditandai {$statusLabel}.");
    }
}
