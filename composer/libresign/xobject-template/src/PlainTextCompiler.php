<?php

// SPDX-FileCopyrightText: 2026 LibreSign
// SPDX-License-Identifier: AGPL-3.0-or-later
declare (strict_types=1);
namespace OCA\Libresign\Vendor\LibreSign\XObjectTemplate;

use InvalidArgumentException;
use OCA\Libresign\Vendor\LibreSign\XObjectTemplate\Dto\CompileResult;
use OCA\Libresign\Vendor\LibreSign\XObjectTemplate\Dto\PlainTextBlock;
use OCA\Libresign\Vendor\LibreSign\XObjectTemplate\Layout\LayoutResult;
use OCA\Libresign\Vendor\LibreSign\XObjectTemplate\Layout\LayoutStyleResolver;
use OCA\Libresign\Vendor\LibreSign\XObjectTemplate\Layout\TextBoxLayouter;
use OCA\Libresign\Vendor\LibreSign\XObjectTemplate\Pdf\StandardFontMetrics;
use OCA\Libresign\Vendor\LibreSign\XObjectTemplate\Pdf\TemplateDocumentBuilder;
/** Compiles literal text without HTML parsing or template interpolation.
 * @internal
 */
final readonly class PlainTextCompiler
{
    private TextBoxLayouter $layouter;
    public function __construct(private TemplateDocumentBuilder $documentBuilder = new TemplateDocumentBuilder())
    {
        $metrics = new StandardFontMetrics($documentBuilder->unicodeFonts(), $documentBuilder->complexShaper());
        $this->layouter = new TextBoxLayouter(new LayoutStyleResolver(), $metrics);
    }
    /** @param list<PlainTextBlock> $blocks */
    public function compile(array $blocks, float $width, float $height) : CompileResult
    {
        if (!\is_finite($width) || !\is_finite($height) || $width <= 0.0 || $height <= 0.0) {
            throw new InvalidArgumentException('Canvas dimensions must be finite and positive.');
        }
        $start = \hrtime(\true);
        $lines = [];
        foreach ($blocks as $block) {
            \array_push($lines, ...$this->layouter->layoutLiteral($block, $height));
        }
        return $this->documentBuilder->buildLayout(new LayoutResult($lines, []), $width, $height, $start, 0);
    }
}
