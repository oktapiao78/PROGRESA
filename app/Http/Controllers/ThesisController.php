<?php

namespace App\Http\Controllers;

use App\Enums\ThesisStatus;
use App\Enums\UserRole;
use App\Models\AuditLog;
use App\Models\ChapterTemplate;
use App\Models\StudyProgram;
use App\Models\Thesis;
use App\Models\ThesisChapter;
use App\Models\User;
use App\Services\AnalyticsAndRiskService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ThesisController extends Controller
{
    protected AnalyticsAndRiskService $analyticsService;

    public function __construct(AnalyticsAndRiskService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function index(Request $request): View
    {
        $user = auth()->user();
        $query = Thesis::with(['student', 'supervisor1', 'supervisor2', 'studyProgram', 'chapters', 'latestStagnation']);

        if ($user->isMahasiswa()) {
            $query->where('student_id', $user->id);
        } elseif ($user->isDosen()) {
            $query->where(fn($q) => $q->where('supervisor_1_id', $user->id)->orWhere('supervisor_2_id', $user->id));
        } elseif ($user->isKoordinator() || $user->isKaprodi()) {
            if ($user->study_program_id) {
                $query->where('study_program_id', $user->study_program_id);
            }
        }

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('student', fn($sq) => $sq->where('name', 'like', "%{$search}%")->orWhere('nim_nip', 'like', "%{$search}%"));
            });
        }

        $theses = $query->latest()->paginate(12)->withQueryString();

        return view('theses.index', compact('theses'));
    }

    public function show(Thesis $thesis): View
    {
        $user = auth()->user();

        // Check permission
        if ($user->isMahasiswa() && $thesis->student_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat skripsi ini.');
        }
        if ($user->isDosen() && !$thesis->isSupervisor($user)) {
            abort(403, 'Anda bukan dosen pembimbing untuk skripsi ini.');
        }

        $this->analyticsService->analyzeThesis($thesis);

        $thesis->load([
            'student.studyProgram',
            'supervisor1',
            'supervisor2',
            'studyProgram',
            'chapters.currentVersion',
            'chapters.revisionItems.supervisor',
            'chapters.approvals.approver',
            'supervisionRequests.supervisor',
            'supervisionRequests.slot',
            'latestStagnation',
        ]);

        return view('theses.show', compact('thesis'));
    }

    public function edit(Thesis $thesis): View
    {
        $user = auth()->user();
        if (!($user->isAdmin() || $user->isKoordinator() || $thesis->student_id === $user->id)) {
            abort(403);
        }

        $supervisors = User::where('role', UserRole::DOSEN)->orderBy('name')->get();
        return view('theses.edit', compact('thesis', 'supervisors'));
    }

    public function update(Request $request, Thesis $thesis): RedirectResponse
    {
        $user = auth()->user();
        if (!($user->isAdmin() || $user->isKoordinator() || $thesis->student_id === $user->id)) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:500'],
            'abstract' => ['nullable', 'string'],
            'target_completion_date' => ['nullable', 'date'],
            'supervisor_1_id' => ['nullable', 'exists:users,id'],
            'supervisor_2_id' => ['nullable', 'exists:users,id'],
            'status' => ['nullable', 'string'],
        ]);

        $thesis->update($validated);
        AuditLog::log('THESIS_UPDATED', $thesis, "Data skripsi '{$thesis->title}' diperbarui.");

        return redirect()->route('theses.show', $thesis)->with('success', 'Data skripsi berhasil diperbarui.');
    }
}
