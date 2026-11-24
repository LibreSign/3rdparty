<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\Factory;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\CertificateCredentialsDto;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\PdfContentDto;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SigningOptionsDto;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SignPdfRequestDto;
/** @internal */
final class SignPdfRequestFactory
{
    public function fromParts(PdfContentDto $pdf, CertificateCredentialsDto $certificate, ?SigningOptionsDto $options = null) : SignPdfRequestDto
    {
        return SignPdfRequestDto::fromRequired($pdf, $certificate, $options ?? SigningOptionsDto::empty());
    }
}
