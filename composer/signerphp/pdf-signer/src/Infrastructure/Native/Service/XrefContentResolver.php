<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Service;

use OCA\Libresign\Vendor\SignerPHP\PdfCore\Buffer;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\PdfDocument;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract\XrefContentResolverInterface;
/** @internal */
final class XrefContentResolver implements XrefContentResolverInterface
{
    public function resolve(PdfDocument $pdfDocument, array $objectOffsets, int $xrefOffset) : Buffer
    {
        return (new \OCA\Libresign\Vendor\SignerPHP\PdfCore\Service\XrefContentResolver())->resolve($pdfDocument, $objectOffsets, $xrefOffset);
    }
}
