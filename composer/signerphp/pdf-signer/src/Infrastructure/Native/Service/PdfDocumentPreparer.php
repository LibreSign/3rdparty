<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Service;

use OCA\Libresign\Vendor\SignerPHP\PdfCore\PdfDocument;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract\PdfDocumentPreparerInterface;
/** @internal */
final class PdfDocumentPreparer implements PdfDocumentPreparerInterface
{
    public function prepare(string $pdfContent) : PdfDocument
    {
        return (new \OCA\Libresign\Vendor\SignerPHP\PdfCore\Service\PdfDocumentPreparer())->prepare($pdfContent);
    }
}
