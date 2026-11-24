<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Service;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract\PdfSignatureExtractorInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\ValueObject\ExtractedPdfSignature;
/** @internal */
final class PdfSignatureExtractor implements PdfSignatureExtractorInterface
{
    /**
     * @return array<int, ExtractedPdfSignature>
     */
    public function extract(string $pdfContent) : array
    {
        $signatures = [];
        $matches = [];
        \preg_match_all('/\\/ByteRange\\s*\\[\\s*(\\d+)\\s+(\\d+)\\s+(\\d+)\\s+(\\d+)\\s*\\]/', $pdfContent, $matches, \PREG_OFFSET_CAPTURE);
        $byteRangeMatches = $matches[0] ?? [];
        foreach ($byteRangeMatches as $index => $fullMatch) {
            $offset = $fullMatch[1];
            $byteRange = [(int) $matches[1][$index][0], (int) $matches[2][$index][0], (int) $matches[3][$index][0], (int) $matches[4][$index][0]];
            $dictionary = $this->resolveCandidateDictionary($pdfContent, $offset);
            // Signature dictionaries and document timestamps (ISO 32000-2, 12.8.5) both carry a ByteRange.
            if (!\preg_match('/\\/Type\\s*\\/(?:Sig|DocTimeStamp)\\b/', $dictionary)) {
                continue;
            }
            if (!\preg_match('/\\/Contents\\s*<([0-9A-Fa-f\\s]+)>/s', $dictionary, $contentMatch)) {
                continue;
            }
            $signatureHex = \preg_replace('/\\s+/', '', $contentMatch[1]) ?? '';
            [$byteRangeValid, $byteRangeError, $signedContent] = $this->buildSignedContent($pdfContent, $byteRange);
            $signatures[] = new ExtractedPdfSignature(index: \count($signatures), byteRange: $byteRange, signatureHex: $signatureHex, signedContent: $signedContent, byteRangeValid: $byteRangeValid, byteRangeError: $byteRangeError, subFilter: \preg_match('/\\/SubFilter\\s*\\/([^\\s\\/\\[\\]<>()]+)/', $dictionary, $subFilterMatch) === 1 ? $subFilterMatch[1] : null);
        }
        return $signatures;
    }
    private function resolveCandidateDictionary(string $pdfContent, int $byteRangeOffset) : string
    {
        $start = \strrpos(\substr($pdfContent, 0, $byteRangeOffset), '<<');
        $end = \strpos($pdfContent, '>>', $byteRangeOffset);
        if ($start === \false || $end === \false || $end <= $start) {
            return '';
        }
        return \substr($pdfContent, $start, $end - $start + 2);
    }
    /**
     * @param  array{0:int,1:int,2:int,3:int}  $byteRange
     * @return array{0:bool,1:?string,2:string}
     */
    private function buildSignedContent(string $pdfContent, array $byteRange) : array
    {
        [$offset1, $length1, $offset2, $length2] = $byteRange;
        $documentSize = \strlen($pdfContent);
        if ($offset1 !== 0 || $length1 < 0 || $length2 < 0 || $offset2 < $offset1 + $length1) {
            return [\false, 'Invalid ByteRange boundaries.', ''];
        }
        if ($offset2 + $length2 > $documentSize) {
            return [\false, 'ByteRange exceeds PDF size.', ''];
        }
        $content = \substr($pdfContent, $offset1, $length1) . \substr($pdfContent, $offset2, $length2);
        return [\true, null, $content];
    }
}
