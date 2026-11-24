<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SignatureValidationOptionsDto;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\ValueObject\SignatureTrustVerification;
/** @internal */
interface SignatureTrustVerifierInterface
{
    public function verify(string $signatureHex, SignatureValidationOptionsDto $options) : SignatureTrustVerification;
}
