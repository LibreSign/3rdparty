<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfCore\Service\Inspect;

/** @internal */
final class SignatureDictionaryInspector
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function extractDetails(string $pdfContent) : array
    {
        $matches = [];
        \preg_match_all('/\\/ByteRange\\s*\\[\\s*(\\d+)\\s+(\\d+)\\s+(\\d+)\\s+(\\d+)\\s*\\]/', $pdfContent, $matches, \PREG_OFFSET_CAPTURE);
        $details = [];
        foreach ($matches[0] ?? [] as $fullMatch) {
            $offset = (int) $fullMatch[1];
            $start = \strrpos(\substr($pdfContent, 0, $offset), '<<');
            $end = \strpos($pdfContent, '>>', $offset);
            if ($start === \false || $end === \false || $end <= $start) {
                continue;
            }
            $dictionary = \substr($pdfContent, $start, $end - $start + 2);
            if (!\is_string($dictionary) || !\preg_match('/\\/Type\\s*\\/Sig\\b/', $dictionary)) {
                continue;
            }
            $contentsHexLength = null;
            if (\preg_match('/\\/Contents\\s*<([0-9A-Fa-f\\s]+)>/s', $dictionary, $contentMatch) === 1) {
                $hex = \preg_replace('/\\s+/', '', $contentMatch[1] ?? '');
                if (\is_string($hex)) {
                    $contentsHexLength = \strlen($hex);
                }
            }
            $details[] = ['signature_index' => \count($details), 'filter' => $this->extractDictName($dictionary, 'Filter'), 'subfilter' => $this->extractDictName($dictionary, 'SubFilter'), 'signing_time' => $this->extractDictString($dictionary, 'M'), 'reason' => $this->extractDictString($dictionary, 'Reason'), 'signer_name' => $this->extractDictString($dictionary, 'Name'), 'location' => $this->extractDictString($dictionary, 'Location'), 'has_filter' => \preg_match('/\\/Filter\\b/', $dictionary) === 1, 'has_subfilter' => \preg_match('/\\/SubFilter\\b/', $dictionary) === 1, 'has_reason' => \preg_match('/\\/Reason\\b/', $dictionary) === 1, 'has_name' => \preg_match('/\\/Name\\b/', $dictionary) === 1, 'has_m' => \preg_match('/\\/M\\b/', $dictionary) === 1, 'has_location' => \preg_match('/\\/Location\\b/', $dictionary) === 1, 'has_contact_info' => \preg_match('/\\/ContactInfo\\b/', $dictionary) === 1, 'has_reference' => \preg_match('/\\/Reference\\b/', $dictionary) === 1, 'has_contents' => \preg_match('/\\/Contents\\b/', $dictionary) === 1, 'contents_hex_length' => $contentsHexLength, 'dictionary_span' => ['start_offset' => $start, 'end_offset' => $end + 1]];
        }
        return $details;
    }
    private function extractDictName(string $dict, string $key) : ?string
    {
        if (\preg_match('/\\/' . \preg_quote($key, '/') . '\\s*\\/([^\\s\\/\\[\\]<>()\\r\\n]+)/', $dict, $m) === 1) {
            return $m[1];
        }
        return null;
    }
    private function extractDictString(string $dict, string $key) : ?string
    {
        if (\preg_match('/\\/' . \preg_quote($key, '/') . '\\s*\\(([^)]*)\\)/', $dict, $m) === 1) {
            return $m[1];
        }
        if (\preg_match('/\\/' . \preg_quote($key, '/') . '\\s*<([0-9A-Fa-f\\s]+)>/', $dict, $m) === 1) {
            $hex = \preg_replace('/\\s+/', '', $m[1] ?? '');
            if (\is_string($hex) && \strlen($hex) % 2 === 0) {
                $decoded = @\hex2bin($hex);
                return \is_string($decoded) ? $decoded : null;
            }
        }
        return null;
    }
}
