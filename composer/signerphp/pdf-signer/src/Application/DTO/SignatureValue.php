<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO;

/**
 * Raw signature bytes produced by a signature provider.
 *
 * Providers must return only the cryptographic signature (for example the
 * output of openssl_sign). Detached CMS/PKCS#7 assembly happens in pdf-signer.
 * @internal
 */
final readonly class SignatureValue
{
    public function __construct(public string $bytes)
    {
    }
}
