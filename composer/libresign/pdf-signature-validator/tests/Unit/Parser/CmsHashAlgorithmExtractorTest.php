<?php

// SPDX-FileCopyrightText: 2026 LibreCode coop and contributors
// SPDX-License-Identifier: AGPL-3.0-or-later
declare (strict_types=1);
namespace OCA\Libresign\Vendor\LibreSign\PdfSignatureValidator\Tests\Unit\Parser;

use OCA\Libresign\Vendor\LibreSign\PdfSignatureValidator\Parser\CmsHashAlgorithmExtractor;
use OCA\Libresign\Vendor\PHPUnit\Framework\TestCase;
/** @internal */
final class CmsHashAlgorithmExtractorTest extends TestCase
{
    public function testReturnsNullForInvalidCmsPayload() : void
    {
        $extractor = new CmsHashAlgorithmExtractor();
        $this->assertNull($extractor->extract('not-der'));
    }
}
