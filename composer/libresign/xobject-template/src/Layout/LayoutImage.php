<?php

// SPDX-FileCopyrightText: 2026 LibreSign
// SPDX-License-Identifier: AGPL-3.0-or-later
declare (strict_types=1);
namespace OCA\Libresign\Vendor\LibreSign\XObjectTemplate\Layout;

/** @internal */
final readonly class LayoutImage
{
    /**
     * @param array{x: float, y: float, width: float, height: float}|null $clipBox
     */
    public function __construct(public string $alias, public float $x, public float $y, public float $width, public float $height, public string $source, public ?array $clipBox = null)
    {
    }
}
