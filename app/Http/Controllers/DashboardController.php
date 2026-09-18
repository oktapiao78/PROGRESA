<?php

namespace App\Http\Controllers;

use App\Enums\ChapterStatus;
use App\Enums\SupervisionStatus;
use App\Enums\UserRole;
use App\Models\AuditLog;
use App\Models\ChapterTemplate;
use App\Models\RevisionItem;
use App\Models\StudyProgram;
use App\Models\SupervisionRequest;
use App\Models\SupervisionSlot;
use App\Models\Thesis;
use App\Models\ThesisChapter;
use App\Models\User;
use App\Services\AnalyticsAndRiskService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    protected AnalyticsAndRiskService $analyticsService;

    public function __construct(AnalyticsAndRiskService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function index(): View
    {
        $user = auth()->user();

        return match ($user->role) {
            UserRole::MAHASISWA => $this->mahasiswaDashboard($user),
            UserRole::DOSEN => $this->dosenDashboard($user),
            UserRole::KOORDINATOR => $this->koordinatorDashboard($user),
            UserRole::KAPRODI => $this->kaprodiDashboard($user),
            UserRole::ADMIN => $this->adminDashboard($user),
        };
    }

    protected function mahasiswaDashboard(User $student): View
    {
        $thesis = $student->studentThesis()
            ->with(['supervisor1', 'supervisor2', 'chapters.currentVersion', 'chapters.revisionItems', 'chapters.approvals'])
            ->first();

        $openRevisions = [];
        $upcomingSupervisions = [];

        if ($thesis) {
            // Update stagnation status
            $this->analyticsService->analyzeThesis($thesis);

            $openRevisions = RevisionItem::whereHas('chapter', fn($q) => $q->where('thesis_id', $thesis->id))
                ->where('is_resolved', false)
                ->with(['chapter', 'supervisor'])
                ->get();

            $upcomingSupervisions = SupervisionRequest::where('student_id', $student->id)
                ->whereIn('status', [SupervisionStatus::REQUESTED, SupervisionStatus::ACCEPTED])
                ->with(['supervisor', 'slot'])
                ->orderBy('scheduled_at')
                ->get();
        }

        return view('dashboard.mahasiswa', compact('student', 'thesis', 'openRevisions', 'upcomingSupervisions'));
    }

    protected function dosenDashboard(User $dosen): View
    {
        $thesesAsP1 = Thesis::where('supervisor_1_id', $dosen->id)
            ->with(['student', 'chapters.approvals', 'latestStagnation'])
            ->get();

        $thesesAsP2 = Thesis::where('supervisor_2_id', $dosen->id)
            ->with(['student', 'chapters.approvals', 'latestStagnation'])
            ->get();

        // Pending chapter reviews
        $pendingChapters = ThesisChapter::whereHas('thesis', function ($q) use ($dosen) {
                $q->where('supervisor_1_id', $dosen->id)->orWhere('supervisor_2_id', $dosen->id);
            })
            ->whereIn('status', [ChapterStatus::SUBMITTED, ChapterStatus::IN_REVIEW])
            ->with(['thesis.student', 'currentVersion'])
            ->get();

        // Pending supervision requests
        $pendingRequests = SupervisionRequest::where('supervisor_id', $dosen->id)
            ->where('status', SupervisionStatus::REQUESTED)
            ->with(['student', 'thesis', 'slot'])
            ->latest()
            ->get();

        // Active slots
        $activeSlots = SupervisionSlot::where('supervisor_id', $dosen->id)
            ->where('is_active', true)
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        return view('dashboard.dosen', compact('dosen', 'thesesAsP1', 'thesesAsP2', 'pendingChapters', 'pendingRequests', 'activeSlots'));
    }

    protected function koordinatorDashboard(User $user): View
    {
        $metrics = $this->analyticsService->getAggregatedMetrics($user->study_program_id);

        $theses = Thesis::with(['student', 'supervisor1', 'supervisor2', 'chapters', 'latestStagnation'])
            ->latest()
            ->paginate(15);

        return view('dashboard.koordinator', compact('metrics', 'theses'));
    }

    protected function kaprodiDashboard(User $user): View
    {
        $metrics = $this->analyticsService->getAggregatedMetrics($user->study_program_id);

        return view('dashboard.kaprodi', compact('metrics'));
    }

    protected function adminDashboard(User $user): View
    {
        $stats = [
            'users_count' => User::count(),
            'students_count' => User::where('role', UserRole::MAHASISWA)->count(),
            'lecturers_count' => User::where('role', UserRole::DOSEN)->count(),
            'theses_count' => Thesis::count(),
            'study_programs_count' => StudyProgram::count(),
            'chapter_templates_count' => ChapterTemplate::count(),
        ];

        $recentLogs = AuditLog::with('user')->latest()->take(10)->get();

        return view('dashboard.admin', compact('stats', 'recentLogs'));
    }
}
