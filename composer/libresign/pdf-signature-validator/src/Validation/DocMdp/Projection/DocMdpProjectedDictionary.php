<?php

// SPDX-FileCopyrightText: 2026 LibreCode coop and contributors
// SPDX-License-Identifier: AGPL-3.0-or-later
declare (strict_types=1);
namespace OCA\Libresign\Vendor\LibreSign\PdfSignatureValidator\Validation\DocMdp\Projection;

use OCA\Libresign\Vendor\LibreSign\PdfSignatureValidator\Pdf\Revision\PdfRevisionObjects;
use UnexpectedValueException;
/**
 * @psalm-import-type PdfObject from \LibreSign\PdfSignatureValidator\Pdf\Revision\PdfIncrementalRevisionReader
 * @phpstan-import-type PdfObject from \LibreSign\PdfSignatureValidator\Pdf\Revision\PdfIncrementalRevisionReader
 * @internal
 */
final class DocMdpProjectedDictionary
{
    /** @param array<string,string> $entries */
    public function setEntry(array &$entries, string $key, ?string $value) : void
    {
        if ($value === null) {
            unset($entries[$key]);
            return;
        }
        $entries[$key] = $value;
    }
    /**
     * @param PdfObject $object
     * @param array<string,string> $entries
     * @return PdfObject
     */
    public function withEntries(array $object, array $entries) : array
    {
        if ($object['entries'] === null || $object['stream'] !== null) {
            throw new UnexpectedValueException('Invalid projected dictionary');
        }
        $object['entries'] = $entries;
        $object['value'] = PdfRevisionObjects::dictionaryValue($entries);
        return $object;
    }
}
