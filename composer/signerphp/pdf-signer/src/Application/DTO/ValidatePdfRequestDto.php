<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO;

/** @internal */
final readonly class ValidatePdfRequestDto
{
    public function __construct(public PdfContentDto $pdf, public SignatureValidationOptionsDto $options = new SignatureValidationOptionsDto())
    {
    }
}
