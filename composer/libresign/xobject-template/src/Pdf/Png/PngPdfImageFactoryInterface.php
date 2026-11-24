<?php

// SPDX-FileCopyrightText: 2026 LibreSign
// SPDX-License-Identifier: AGPL-3.0-or-later
declare (strict_types=1);
namespace OCA\Libresign\Vendor\LibreSign\XObjectTemplate\Pdf\Png;

use OCA\Libresign\Vendor\LibreSign\XObjectTemplate\Pdf\EmbeddedPdfImage;
/** @internal */
interface PngPdfImageFactoryInterface
{
    public function create(string $contents) : EmbeddedPdfImage;
}
