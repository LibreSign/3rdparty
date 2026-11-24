<?php

// SPDX-FileCopyrightText: 2026 LibreSign
// SPDX-License-Identifier: AGPL-3.0-or-later
declare (strict_types=1);
namespace OCA\Libresign\Vendor\LibreSign\XObjectTemplate;

use OCA\Libresign\Vendor\LibreSign\XObjectTemplate\Contract\XObjectTemplateCompilerInterface;
use OCA\Libresign\Vendor\LibreSign\XObjectTemplate\Dto\CompileRequest;
use OCA\Libresign\Vendor\LibreSign\XObjectTemplate\Dto\CompileResult;
use OCA\Libresign\Vendor\LibreSign\XObjectTemplate\Exception\UnsupportedSubsetException;
use OCA\Libresign\Vendor\LibreSign\XObjectTemplate\Html\HtmlContextInterpolator;
use OCA\Libresign\Vendor\LibreSign\XObjectTemplate\Html\SubsetHtmlParser;
use OCA\Libresign\Vendor\LibreSign\XObjectTemplate\Layout\LinearLayoutEngine;
use OCA\Libresign\Vendor\LibreSign\XObjectTemplate\Pdf\ColorParser;
use OCA\Libresign\Vendor\LibreSign\XObjectTemplate\Pdf\PdfEscaper;
use OCA\Libresign\Vendor\LibreSign\XObjectTemplate\Pdf\TemplateDocumentBuilder;
use OCA\Libresign\Vendor\LibreSign\XObjectTemplate\Pdf\StandardFontMetrics;
/** @internal */
final readonly class XObjectTemplateCompiler implements XObjectTemplateCompilerInterface
{
    private SubsetHtmlParser $htmlParser;
    private HtmlContextInterpolator $contextInterpolator;
    private LinearLayoutEngine $layoutEngine;
    private TemplateDocumentBuilder $documentBuilder;
    public function __construct(?SubsetHtmlParser $htmlParser = null, ?LinearLayoutEngine $layoutEngine = null, ?PdfEscaper $pdfEscaper = null, ?ColorParser $colorParser = null, ?TemplateDocumentBuilder $documentBuilder = null, ?HtmlContextInterpolator $contextInterpolator = null)
    {
        $this->htmlParser = $htmlParser ?? new SubsetHtmlParser();
        $this->contextInterpolator = $contextInterpolator ?? new HtmlContextInterpolator();
        $this->documentBuilder = $documentBuilder ?? new TemplateDocumentBuilder($pdfEscaper ?? new PdfEscaper(), $colorParser ?? new ColorParser());
        $this->layoutEngine = $layoutEngine ?? new LinearLayoutEngine(fontMetrics: new StandardFontMetrics($this->documentBuilder->unicodeFonts(), $this->documentBuilder->complexShaper()));
    }
    /**
     * Compile the supported HTML+CSS subset into a reusable PDF Form XObject payload.
     *
     * @throws UnsupportedSubsetException If the HTML fragment contains an unsupported element.
     */
    public function compile(CompileRequest $request) : CompileResult
    {
        $start = \hrtime(\true);
        $nodes = $this->htmlParser->parse($this->contextInterpolator->interpolate($request->html, $request->context));
        $layout = $this->layoutEngine->layout($nodes, $request->width, $request->height);
        return $this->documentBuilder->build($request, $layout, $start, \count($nodes));
    }
}
