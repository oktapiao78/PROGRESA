<?php

namespace App\Services;

use ZipArchive;

class DocumentTextExtractor
{
    /**
     * Extract raw text from file according to extension.
     */
    public function extractText(string $filePath, string $extension): ?string
    {
        $ext = strtolower($extension);

        try {
            return match ($ext) {
                'txt', 'md', 'csv' => $this->extractFromPlainText($filePath),
                'docx' => $this->extractFromDocx($filePath),
                'pdf' => $this->extractFromPdf($filePath),
                default => null,
            };
        } catch (\Throwable $e) {
            \Log::warning("Failed extracting text from {$filePath}: " . $e->getMessage());
            return null;
        }
    }

    protected function extractFromPlainText(string $path): ?string
    {
        if (!file_exists($path)) {
            return null;
        }
        $content = file_get_contents($path);
        return mb_convert_encoding($content, 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252');
    }

    /**
     * Native DOCX text extraction using ZipArchive and document.xml parsing.
     */
    protected function extractFromDocx(string $path): ?string
    {
        if (!file_exists($path)) {
            return null;
        }

        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            return null;
        }

        $xmlIndex = $zip->locateName('word/document.xml');
        if ($xmlIndex === false) {
            $zip->close();
            return null;
        }

        $xmlData = $zip->getFromIndex($xmlIndex);
        $zip->close();

        if (!$xmlData) {
            return null;
        }

        // Parse paragraphs
        $xml = simplexml_load_string($xmlData, 'SimpleXMLElement', LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
        if (!$xml) {
            return null;
        }

        $xml->registerXPathNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
        $paragraphs = $xml->xpath('//w:p');

        $lines = [];
        foreach ($paragraphs as $p) {
            $texts = $p->xpath('.//w:t');
            $line = '';
            foreach ($texts as $t) {
                $line .= (string) $t;
            }
            if (trim($line) !== '') {
                $lines[] = trim($line);
            }
        }

        return implode("\n\n", $lines);
    }

    /**
     * Fallback PDF text reader.
     */
    protected function extractFromPdf(string $path): ?string
    {
        if (!file_exists($path)) {
            return null;
        }

        $content = file_get_contents($path);
        if (!$content) {
            return null;
        }

        // Basic ASCII text stream extraction from uncompressed PDF blocks
        $text = '';
        if (preg_match_all('/\(([^\)]+)\)\s*T[jJ]/', $content, $matches)) {
            $text = implode(' ', $matches[1]);
        } elseif (preg_match_all('/\[([^\]]+)\]\s*TJ/', $content, $matches)) {
            $text = implode(' ', $matches[1]);
        }

        return !empty(trim($text)) ? trim($text) : null;
    }
}
