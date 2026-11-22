<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfCore\Service\Inspect;

/** @internal */
class PdfStructureInspector
{
    /**
     * @return array{mode:string,has_xref_table:bool,has_xref_stream:bool,startxref:int|null}
     */
    public function detectXrefMode(string $pdfContent) : array
    {
        $hasXrefTable = \preg_match('/(?:^|\\R)xref\\R/s', $pdfContent) === 1;
        $hasXrefStream = \preg_match('/\\/Type\\s*\\/XRef\\b/', $pdfContent) === 1;
        $mode = 'unknown';
        if ($hasXrefTable && $hasXrefStream) {
            $mode = 'hybrid';
        } elseif ($hasXrefStream) {
            $mode = 'stream';
        } elseif ($hasXrefTable) {
            $mode = 'table';
        }
        $startxref = null;
        if (\preg_match_all('/startxref\\s*([0-9]+)\\s*%%EOF/ms', $pdfContent, $matches) > 0) {
            $startxrefMatches = $matches[1] ?? [];
            $last = $startxrefMatches === [] ? null : \end($startxrefMatches);
            if (\is_string($last) && $last !== '') {
                $startxref = (int) $last;
            }
        }
        return ['mode' => $mode, 'has_xref_table' => $hasXrefTable, 'has_xref_stream' => $hasXrefStream, 'startxref' => $startxref];
    }
    /**
     * @return array<int, array{index:int,startxref:int,eof_offset:int,revision_start:int,revision_end:int}>
     */
    public function detectIncrementalRevisions(string $pdfContent) : array
    {
        $matches = [];
        \preg_match_all('/startxref\\s*([0-9]+)\\s*%%EOF/ms', $pdfContent, $matches, \PREG_OFFSET_CAPTURE);
        $revisions = [];
        $previousEnd = -1;
        foreach ($matches[0] ?? [] as $index => $match) {
            $full = $match[0];
            $offset = (int) $match[1];
            $eofEnd = $offset + \strlen($full) - 1;
            $startxref = isset($matches[1][$index][0]) ? (int) $matches[1][$index][0] : 0;
            $revisionStart = $previousEnd + 1;
            $revisions[] = ['index' => $index, 'startxref' => $startxref, 'eof_offset' => $offset, 'revision_start' => \max(0, $revisionStart), 'revision_end' => \max(0, $eofEnd)];
            $previousEnd = $eofEnd;
        }
        return $revisions;
    }
    public function detectPdfVersion(string $pdfContent) : ?string
    {
        if (\preg_match('/^%PDF-(\\d+\\.\\d+)/m', $pdfContent, $m) === 1) {
            return $m[1];
        }
        return null;
    }
    /**
     * @return array{has_appearance:bool,widget_count:int,ap_n_count:int,xobject_count:int,trace:array<int, string>}
     */
    public function analyzeAppearance(string $pdfContent) : array
    {
        $widgetCount = (int) \preg_match_all('/\\/Subtype\\s*\\/Widget\\b/', $pdfContent, $unused);
        $apNCount = (int) \preg_match_all('/\\/AP\\s*<<[^>]*\\/N\\b/s', $pdfContent, $unused2);
        $xObjectCount = (int) \preg_match_all('/\\/Type\\s*\\/XObject\\b/', $pdfContent, $unused3);
        $trace = [];
        if ($widgetCount > 0) {
            $trace[] = 'widget-annotations-detected';
        }
        if ($apNCount > 0) {
            $trace[] = 'appearance-normal-stream-detected';
        }
        if ($xObjectCount > 0) {
            $trace[] = 'xobject-resources-detected';
        }
        return ['has_appearance' => $widgetCount > 0 || $apNCount > 0, 'widget_count' => $widgetCount, 'ap_n_count' => $apNCount, 'xobject_count' => $xObjectCount, 'trace' => $trace];
    }
}
