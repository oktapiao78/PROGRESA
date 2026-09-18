<?php

namespace App\Http\Controllers;

use App\Models\DocumentVersion;
use App\Models\ThesisChapter;
use App\Services\DocumentDiffService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentDiffController extends Controller
{
    protected DocumentDiffService $diffService;

    public function __construct(DocumentDiffService $diffService)
    {
        $this->diffService = $diffService;
    }

    public function compare(Request $request, ThesisChapter $chapter): View
    {
        $user = auth()->user();
        $thesis = $chapter->thesis;

        // Authorization
        $isStudent = $thesis->student_id === $user->id;
        $isSupervisor = $thesis->isSupervisor($user);
        $isStaff = $user->isKoordinator() || $user->isKaprodi() || $user->isAdmin();

        if (!($isStudent || $isSupervisor || $isStaff)) {
            abort(403);
        }

        $allVersions = $chapter->versions()->orderBy('version_number')->get();

        if ($allVersions->count() < 2) {
            return view('chapters.diff', [
                'chapter' => $chapter,
                'allVersions' => $allVersions,
                'diffData' => null,
                'message' => 'Bab ini memerlukan minimal 2 versi dokumen untuk dapat dibandingkan.',
            ]);
        }

        // Determine which versions to compare
        $v1Id = $request->query('v1');
        $v2Id = $request->query('v2');

        if ($v1Id && $v2Id) {
            $oldVersion = $allVersions->firstWhere('id', $v1Id);
            $newVersion = $allVersions->firstWhere('id', $v2Id);
        } else {
            // Default to comparing the second-to-last with the latest
            $newVersion = $allVersions->last();
            $oldVersion = $allVersions[$allVersions->count() - 2] ?? $newVersion;
        }

        if (!$oldVersion || !$newVersion) {
            $newVersion = $allVersions->last();
            $oldVersion = $allVersions->first();
        }

        $diffData = $this->diffService->compare($oldVersion, $newVersion);

        return view('chapters.diff', compact('chapter', 'allVersions', 'diffData', 'oldVersion', 'newVersion'));
    }
}
