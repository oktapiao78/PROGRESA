<?php

namespace App\Http\Controllers;

use App\Enums\ChapterStatus;
use App\Enums\SupervisionStatus;
use App\Models\RevisionItem;
use App\Models\SupervisionRequest;
use App\Models\Thesis;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user && $user->isDosen()) {
            return redirect()->route('dashboard.dosen');
        }

        return redirect()->route('dashboard.mahasiswa');
    }

    public function mahasiswaDashboard()
    {
        $student = Auth::user();

        $studentThesis = Thesis::with([
            'supervisor1',
            'supervisor2',
            'latestStagnation',
            'chapters.currentVersion',
            'chapters.revisionItems',
            'chapters.openRevisions',
        ])
            ->where('student_id', $student->id)
            ->first();

        $openRevisions = collect();
        $scheduledSessions = collect();
        $thesis = null;

        if ($studentThesis) {
            $totalChapters = $studentThesis->chapters->count();
            $approvedChapters = $studentThesis->chapters->where('status', ChapterStatus::APPROVED)->count();
            $uploadedChapters = $studentThesis->chapters->filter(fn ($c) => $c->current_version_id !== null)->count();

            $thesis = (object) [
                'id' => $studentThesis->id,
                'title' => $studentThesis->title,
                'objective' => $studentThesis->abstract ?? 'Belum ada deskripsi / abstrak penelitian.',
                'status' => $studentThesis->status ? $studentThesis->status->label() : 'Bimbingan Berjalan',
                'risk_level' => $studentThesis->latestStagnation?->risk_level
                    ? $studentThesis->latestStagnation->risk_level->label()
                    : 'Risiko Rendah',
                'progress_percentage' => $studentThesis->progress_percentage ?? 0,
                'approved_count' => $approvedChapters,
                'uploaded_count' => $uploadedChapters,
                'total_chapters' => $totalChapters,
                'supervisor1' => (object) ['name' => $studentThesis->supervisor1?->name ?? 'Belum Ditentukan'],
                'supervisor2' => (object) ['name' => $studentThesis->supervisor2?->name ?? 'Belum Ditentukan'],
                'chapters' => $studentThesis->chapters->map(function ($c) {
                    return (object) [
                        'id' => $c->id,
                        'order' => $c->order,
                        'title' => $c->title,
                        'status_enum' => $c->status,
                        'status' => $c->status ? $c->status->label() : 'Belum Dimulai',
                        'version' => $c->currentVersion?->version_number ?? 0,
                        'revisions_open' => $c->openRevisions->count(),
                        'revisions_total' => $c->revisionItems->count(),
                    ];
                }),
            ];

            $chapterIds = $studentThesis->chapters->pluck('id');
            $openRevisions = RevisionItem::with(['chapter', 'supervisor'])
                ->whereIn('thesis_chapter_id', $chapterIds)
                ->where('is_resolved', false)
                ->latest()
                ->get();

            $scheduledSessions = SupervisionRequest::with(['supervisor', 'slot'])
                ->where('student_id', $student->id)
                ->whereIn('status', [SupervisionStatus::ACCEPTED, SupervisionStatus::REQUESTED])
                ->orderBy('scheduled_at')
                ->get();
        }

        return view('dashboard.mahasiswa', compact('student', 'thesis', 'openRevisions', 'scheduledSessions'));
    }

    public function dosenDashboard()
    {
        $user = Auth::user();

        $p1Count = Thesis::where('supervisor_1_id', $user->id)->count();
        $p2Count = Thesis::where('supervisor_2_id', $user->id)->count();

        $theses = Thesis::with(['student', 'latestStagnation', 'chapters.currentVersion'])
            ->where(function ($q) use ($user) {
                $q->where('supervisor_1_id', $user->id)
                  ->orWhere('supervisor_2_id', $user->id);
            })
            ->get();

        $pendingDrafts = $theses->flatMap(function ($t) {
            return $t->chapters
                ->whereIn('status', [ChapterStatus::SUBMITTED, ChapterStatus::IN_REVIEW])
                ->map(function ($ch) use ($t) {
                    return (object) [
                        'chapter_id' => $ch->id,
                        'chapter_title' => $ch->title,
                        'student_name' => $t->student?->name ?? 'Mahasiswa',
                        'student_nim' => $t->student?->nim_nip ?? '-',
                        'thesis_title' => $t->title,
                        'version' => $ch->currentVersion?->version_number ?? 1,
                        'updated_at' => $ch->updated_at,
                    ];
                });
        })->values();

        $draftCount = $pendingDrafts->count();

        $requestCount = SupervisionRequest::where('supervisor_id', $user->id)
            ->where('status', SupervisionStatus::REQUESTED)
            ->count();

        $stats = (object) [
            'p1_count' => $p1Count,
            'p2_count' => $p2Count,
            'draft_count' => $draftCount,
            'request_count' => $requestCount,
        ];

        $mahasiswa_list = $theses->map(function ($t) use ($user) {
            return (object) [
                'id' => $t->id,
                'nama' => $t->student?->name ?? 'Mahasiswa',
                'nim' => $t->student?->nim_nip ?? '-',
                'judul' => $t->title,
                'progress' => $t->progress_percentage ?? 0,
                'risk' => $t->latestStagnation?->risk_level ? $t->latestStagnation->risk_level->label() : null,
                'pembimbing' => $t->supervisor_1_id === $user->id ? 'Pembimbing 1' : 'Pembimbing 2',
            ];
        });

        return view('dashboard.dosen', compact('stats', 'mahasiswa_list', 'pendingDrafts'));
    }
}
