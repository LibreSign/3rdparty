<?php

// SPDX-FileCopyrightText: 2026 LibreSign
// SPDX-License-Identifier: AGPL-3.0-or-later
declare (strict_types=1);
namespace OCA\Libresign\Vendor\LibreSign\XObjectTemplate\Css;

/** @internal */
final class InlineStyleParser
{
    public function parse(string $style) : StyleMap
    {
        $result = [];
        foreach (\explode(';', $style) as $chunk) {
            $parts = \explode(':', $chunk, 2);
            if (\count($parts) !== 2) {
                continue;
            }
            $name = \strtolower(\trim($parts[0]));
            $value = \trim($parts[1]);
            if ($name === '' || $value === '') {
                continue;
            }
            $result[$name] = $value;
        }
        return new StyleMap($result);
    }
}
