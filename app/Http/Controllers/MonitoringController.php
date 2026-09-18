<?php

namespace App\Http\Controllers;

use App\Enums\RiskLevel;
use App\Models\Thesis;
use App\Models\User;
use App\Services\AnalyticsAndRiskService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MonitoringController extends Controller
{
    protected AnalyticsAndRiskService $analyticsService;

    public function __construct(AnalyticsAndRiskService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function stagnantStudents(Request $request): View
    {
        $user = auth()->user();
        $query = Thesis::with(['student', 'supervisor1', 'supervisor2', 'latestStagnation', 'chapters']);

        if ($user->study_program_id) {
            $query->where('study_program_id', $user->study_program_id);
        }

        $allTheses = $query->get()->filter(function ($t) {
            return $t->latestStagnation && in_array($t->latestStagnation->risk_level, [
                RiskLevel::MEDIUM,
                RiskLevel::HIGH,
                RiskLevel::CRITICAL,
            ]);
        })->sortByDesc(fn($t) => $t->latestStagnation->days_inactive);

        return view('monitoring.stagnant_students', compact('allTheses'));
    }

    public function supervisorResponse(Request $request): View
    {
        $user = auth()->user();
        $metrics = $this->analyticsService->getAggregatedMetrics($user->study_program_id);

        return view('monitoring.supervisor_response', [
            'supervisors' => $metrics['supervisors'],
        ]);
    }

    public function delayRisk(Request $request): View
    {
        $user = auth()->user();
        $query = Thesis::with(['student', 'supervisor1', 'supervisor2', 'latestStagnation', 'chapters']);

        if ($user->study_program_id) {
            $query->where('study_program_id', $user->study_program_id);
        }

        $theses = $query->get()->map(function ($thesis) {
            $approvedChapters = $thesis->chapters->where('status', \App\Enums\ChapterStatus::APPROVED)->count();
            $totalChapters = max(1, $thesis->chapters->count());
            $ratio = $approvedChapters / $totalChapters;

            $startDate = $thesis->start_date ?? $thesis->created_at;
            $monthsElapsed = max(1, (int) $startDate->diffInMonths(now()));

            $projectedRemainingMonths = $ratio > 0 ? round(($monthsElapsed / $ratio) * (1 - $ratio), 1) : 6;
            $predictedTotalMonths = round($monthsElapsed + $projectedRemainingMonths, 1);

            $delayStatus = $predictedTotalMonths > 8 ? 'Tinggi (Potensi Molor > 2 Semester)' : ($predictedTotalMonths > 6 ? 'Sedang (Melewati 1 Semester)' : 'Aman (Tepat Waktu)');

            return [
                'thesis' => $thesis,
                'months_elapsed' => $monthsElapsed,
                'approved_chapters' => $approvedChapters,
                'total_chapters' => $totalChapters,
                'projected_remaining_months' => $projectedRemainingMonths,
                'predicted_total_months' => $predictedTotalMonths,
                'delay_status' => $delayStatus,
                'risk_level' => $thesis->latestStagnation?->risk_level ?? RiskLevel::ON_TRACK,
            ];
        })->sortByDesc('predicted_total_months');

        return view('monitoring.delay_risk', compact('theses'));
    }
}
