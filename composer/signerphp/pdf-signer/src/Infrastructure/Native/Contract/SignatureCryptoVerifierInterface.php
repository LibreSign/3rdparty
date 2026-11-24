<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\ValueObject\SignatureCryptoVerification;
/** @internal */
interface SignatureCryptoVerifierInterface
{
    public function verify(string $signedContent, string $signatureHex) : SignatureCryptoVerification;
}
