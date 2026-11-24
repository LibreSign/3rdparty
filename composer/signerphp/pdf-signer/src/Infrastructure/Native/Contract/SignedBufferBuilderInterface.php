<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract;

use OCA\Libresign\Vendor\SignerPHP\PdfCore\Buffer;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\PdfDocument;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\Contract\SignatureProviderInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SigningContextDto;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Service\SignatureHandler;
/** @internal */
interface SignedBufferBuilderInterface
{
    public function build(PdfDocument $pdfDocument, SignatureHandler $signatureHandler, SigningContextDto $context, SignatureProviderInterface $signatureProvider) : Buffer;
}
