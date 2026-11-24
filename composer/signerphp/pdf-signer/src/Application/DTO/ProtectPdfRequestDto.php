<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO;

/** @internal */
final readonly class ProtectPdfRequestDto
{
    public function __construct(public PdfContentDto $pdf, public ProtectionOptionsDto $options)
    {
    }
}
