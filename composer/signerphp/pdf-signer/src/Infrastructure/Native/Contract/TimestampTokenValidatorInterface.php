<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract;

/** @internal */
interface TimestampTokenValidatorInterface
{
    /** Verify the cryptographic token signature and its message imprint. */
    public function assertValid(string $tokenDer, string $signatureValue) : void;
}
