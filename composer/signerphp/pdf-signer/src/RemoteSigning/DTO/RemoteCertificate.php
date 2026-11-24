<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\RemoteSigning\DTO;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SigningCertificateDto;
/** @internal */
final readonly class RemoteCertificate
{
    public function __construct(public string $identifier, public SigningCertificateDto $certificate)
    {
    }
}
