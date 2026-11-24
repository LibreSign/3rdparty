<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract;

use OCA\Libresign\Vendor\SignerPHP\PdfCore\PdfDocument;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SigningContextDto;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Service\SignatureHandler;
/** @internal */
interface SignatureFactoryInterface
{
    public function create(SigningContextDto $context, PdfDocument $pdfDocument) : SignatureHandler;
}
