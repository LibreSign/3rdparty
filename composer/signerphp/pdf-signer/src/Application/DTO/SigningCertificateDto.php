<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO;

/** @internal */
final readonly class SigningCertificateDto
{
    /** @param list<string> $chainPem */
    public function __construct(public string $certificatePem, public array $chainPem = [])
    {
    }
}
