<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO;

/** @internal */
final readonly class SigningPayload
{
    public function __construct(public string $data, public HashAlgorithm $algorithm = HashAlgorithm::Sha256)
    {
    }
}
