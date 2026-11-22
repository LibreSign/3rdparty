<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfCore\Service\Inspect;

/** @internal */
final class ByteRangeInspector
{
    /**
     * @param  array<int, array{signature_index:int,byte_range:array{0:int,1:int,2:int,3:int},byte_range_valid:bool,byte_range_error:?string}>  $byteRangeFinalVerification
     * @param  array<int, array<string, mixed>>  $signatureDictionaryDetails
     * @return array<int, array<string, mixed>>
     */
    public function derivePlanning(array $byteRangeFinalVerification, array $signatureDictionaryDetails, int $fileSize) : array
    {
        $dictionaryBySignature = [];
        foreach ($signatureDictionaryDetails as $detail) {
            if (!isset($detail['signature_index']) || !\is_int($detail['signature_index'])) {
                continue;
            }
            $dictionaryBySignature[$detail['signature_index']] = $detail;
        }
        $planning = [];
        foreach ($byteRangeFinalVerification as $entry) {
            $signatureIndex = $entry['signature_index'];
            $byteRange = $entry['byte_range'];
            $reservedContainerLength = $byteRange[2] - $byteRange[1];
            $plannedContentsHexLength = \max(0, $reservedContainerLength - 2);
            $expectedSecondPartOffset = $byteRange[1] + $plannedContentsHexLength + 2;
            $signedSpanEndOffset = $byteRange[2] + $byteRange[3] - 1;
            $gapLength = $byteRange[2] - $byteRange[1];
            $signedSpanLength = $byteRange[1] + $byteRange[3];
            $dictionaryDetails = $dictionaryBySignature[$signatureIndex] ?? null;
            $observedHexLength = null;
            if (\is_array($dictionaryDetails) && isset($dictionaryDetails['contents_hex_length']) && \is_int($dictionaryDetails['contents_hex_length'])) {
                $observedHexLength = $dictionaryDetails['contents_hex_length'];
            }
            $planning[] = ['signature_index' => $signatureIndex, 'planned_contents_hex_length' => $plannedContentsHexLength, 'reserved_container_length' => $reservedContainerLength, 'expected_second_part_offset' => $expectedSecondPartOffset, 'matches_final_second_part_offset' => $expectedSecondPartOffset === $byteRange[2], 'signed_span_end_offset' => $signedSpanEndOffset, 'signed_span_length' => $signedSpanLength, 'covers_eof' => $byteRange[2] + $byteRange[3] === $fileSize, 'gap_length' => $gapLength, 'gap_matches_reserved_container' => $gapLength === $reservedContainerLength, 'observed_contents_hex_length' => $observedHexLength, 'matches_observed_contents_hex_length' => $observedHexLength === null ? null : $observedHexLength === $plannedContentsHexLength];
        }
        return $planning;
    }
    /**
     * @param  array<int, array{index:int,startxref:int,eof_offset:int,revision_start:int,revision_end:int}>  $revisions
     * @param  array<int, array<string, mixed>>  $byteRangePlanning
     * @return array<int, array<string, mixed>>
     */
    public function mapSignaturesToRevisions(array $revisions, array $byteRangePlanning) : array
    {
        $mapped = [];
        foreach ($byteRangePlanning as $entry) {
            if (!isset($entry['signature_index']) || !\is_int($entry['signature_index'])) {
                continue;
            }
            if (!isset($entry['expected_second_part_offset']) || !\is_int($entry['expected_second_part_offset'])) {
                continue;
            }
            $signatureIndex = $entry['signature_index'];
            $secondPartOffset = $entry['expected_second_part_offset'];
            $revisionMatch = null;
            foreach ($revisions as $revision) {
                if ($secondPartOffset <= $revision['revision_end']) {
                    $revisionMatch = $revision;
                    break;
                }
            }
            $mapped[] = ['signature_index' => $signatureIndex, 'second_part_offset' => $secondPartOffset, 'revision_index' => $revisionMatch['index'] ?? null, 'revision_end' => $revisionMatch['revision_end'] ?? null, 'fits_revision_boundary' => $revisionMatch !== null];
        }
        return $mapped;
    }
    /**
     * @param  array<int, array{signature_index:int,byte_range:array{0:int,1:int,2:int,3:int},byte_range_valid:bool,byte_range_error:?string}>  $byteRangeFinalVerification
     * @return array{applicable:bool,all_start_from_zero:bool|null,has_overlap:bool,coverage_progression_ok:bool}
     */
    public function checkCoverageConsistency(array $byteRangeFinalVerification) : array
    {
        if ($byteRangeFinalVerification === []) {
            return ['applicable' => \false, 'all_start_from_zero' => null, 'has_overlap' => \false, 'coverage_progression_ok' => \true];
        }
        $ranges = \array_map(static function (array $entry) : array {
            $byteRange = $entry['byte_range'];
            return ['start2' => $byteRange[2], 'covered_end' => $byteRange[2] + $byteRange[3], 'starts_from_zero' => $byteRange[0] === 0];
        }, $byteRangeFinalVerification);
        \usort($ranges, static fn(array $a, array $b): int => $a['covered_end'] <=> $b['covered_end']);
        $allStartFromZero = \array_reduce($ranges, static fn(bool $carry, array $range): bool => $carry && $range['starts_from_zero'], \true);
        $hasOverlap = \false;
        $orderedRanges = \array_values($ranges);
        $count = \count($orderedRanges);
        for ($i = 0; $i < $count - 1; $i++) {
            $left = $orderedRanges[$i] ?? null;
            if (!\is_array($left)) {
                continue;
            }
            for ($j = $i + 1; $j < $count; $j++) {
                $right = $orderedRanges[$j] ?? null;
                if (!\is_array($right)) {
                    continue;
                }
                if ($left['start2'] < $right['covered_end'] && $right['start2'] < $left['covered_end']) {
                    $hasOverlap = \true;
                    break 2;
                }
            }
        }
        $progressionOk = \true;
        $previousCoveredEnd = null;
        foreach ($orderedRanges as $range) {
            if ($previousCoveredEnd !== null && $range['covered_end'] <= $previousCoveredEnd) {
                $progressionOk = \false;
                break;
            }
            $previousCoveredEnd = $range['covered_end'];
        }
        return ['applicable' => \true, 'all_start_from_zero' => $allStartFromZero, 'has_overlap' => $hasOverlap, 'coverage_progression_ok' => $progressionOk];
    }
}
