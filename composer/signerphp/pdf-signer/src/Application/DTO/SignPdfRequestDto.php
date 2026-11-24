<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO;

/** @internal */
final readonly class SignPdfRequestDto
{
    public function __construct(public PdfContentDto $pdf, public CertificateCredentialsDto $certificate, public SigningOptionsDto $options)
    {
    }
    public static function fromRequired(PdfContentDto $pdf, CertificateCredentialsDto $certificate, ?SigningOptionsDto $options = null) : self
    {
        return new self($pdf, $certificate, $options ?? SigningOptionsDto::empty());
    }
}
