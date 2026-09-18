<?php

namespace App\Services;

use App\Models\DocumentVersion;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;

class DocumentDiffService
{
    /**
     * Compare two document versions and generate visual diff data.
     */
    public function compare(DocumentVersion $oldVersion, DocumentVersion $newVersion): array
    {
        $oldText = $oldVersion->text_content ?? "Dokumen v{$oldVersion->version_number} tidak memiliki teks yang dapat diekstrak atau merupakan file binary (PDF/Scan).";
        $newText = $newVersion->text_content ?? "Dokumen v{$newVersion->version_number} tidak memiliki teks yang dapat diekstrak atau merupakan file binary (PDF/Scan).";

        $oldLines = explode("\n", str_replace(["\r\n", "\r"], "\n", $oldText));
        $newLines = explode("\n", str_replace(["\r\n", "\r"], "\n", $newText));

        // Use Sebastian Differ for precise line changes
        $builder = new UnifiedDiffOutputBuilder("--- v{$oldVersion->version_number}\n+++ v{$newVersion->version_number}\n", false);
        $differ = new Differ($builder);
        $diffString = $differ->diff($oldText, $newText);

        $additions = 0;
        $deletions = 0;
        $unchanged = 0;
        $diffRows = [];

        $diffArray = $differ->diffToArray($oldText, $newText);

        foreach ($diffArray as $item) {
            $type = $item[1]; // 0: unchanged, 1: added, 2: removed
            $line = $item[0];

            if ($type === 1) {
                $additions++;
                $diffRows[] = [
                    'type' => 'ins',
                    'class' => 'bg-emerald-50 text-emerald-900 border-l-4 border-emerald-500',
                    'symbol' => '+',
                    'content' => $line,
                ];
            } elseif ($type === 2) {
                $deletions++;
                $diffRows[] = [
                    'type' => 'del',
                    'class' => 'bg-rose-50 text-rose-900 border-l-4 border-rose-500 line-through opacity-80',
                    'symbol' => '-',
                    'content' => $line,
                ];
            } else {
                $unchanged++;
                $diffRows[] = [
                    'type' => 'same',
                    'class' => 'bg-white text-slate-700 border-l-4 border-transparent',
                    'symbol' => ' ',
                    'content' => $line,
                ];
            }
        }

        // Calculate similarity using similar_text
        similar_text(substr($oldText, 0, 5000), substr($newText, 0, 5000), $similarity);

        return [
            'old_version' => $oldVersion,
            'new_version' => $newVersion,
            'additions' => $additions,
            'deletions' => $deletions,
            'unchanged' => $unchanged,
            'similarity' => round($similarity, 1),
            'diff_rows' => $diffRows,
            'raw_diff' => $diffString,
        ];
    }
}
