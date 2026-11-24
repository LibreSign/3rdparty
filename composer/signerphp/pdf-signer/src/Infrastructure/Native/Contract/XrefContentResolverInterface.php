<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract;

use OCA\Libresign\Vendor\SignerPHP\PdfCore\Buffer;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\PdfDocument;
/** @internal */
interface XrefContentResolverInterface
{
    /**
     * @param  array<int, int>  $objectOffsets
     */
    public function resolve(PdfDocument $pdfDocument, array $objectOffsets, int $xrefOffset) : Buffer;
}
