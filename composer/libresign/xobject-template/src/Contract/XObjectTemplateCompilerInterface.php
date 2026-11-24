<?php

// SPDX-FileCopyrightText: 2026 LibreSign
// SPDX-License-Identifier: AGPL-3.0-or-later
declare (strict_types=1);
namespace OCA\Libresign\Vendor\LibreSign\XObjectTemplate\Contract;

use OCA\Libresign\Vendor\LibreSign\XObjectTemplate\Dto\CompileRequest;
use OCA\Libresign\Vendor\LibreSign\XObjectTemplate\Dto\CompileResult;
use OCA\Libresign\Vendor\LibreSign\XObjectTemplate\Exception\UnsupportedSubsetException;
/** @internal */
interface XObjectTemplateCompilerInterface
{
    /**
     * Compile the supported HTML+CSS subset into a reusable PDF Form XObject payload.
     *
     * @throws UnsupportedSubsetException If the HTML fragment contains an unsupported element.
     */
    public function compile(CompileRequest $request) : CompileResult;
}
