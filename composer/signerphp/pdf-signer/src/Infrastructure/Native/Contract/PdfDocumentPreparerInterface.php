<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract;

use OCA\Libresign\Vendor\SignerPHP\PdfCore\PdfDocument;
/** @internal */
interface PdfDocumentPreparerInterface
{
    public function prepare(string $pdfContent) : PdfDocument;
}
