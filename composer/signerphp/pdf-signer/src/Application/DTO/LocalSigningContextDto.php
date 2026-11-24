<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Domain\ValueObject\VerifiedCertificate;
/** @internal */
final readonly class LocalSigningContextDto
{
    public function __construct(public SigningContextDto $signingContext, public VerifiedCertificate $verifiedCertificate)
    {
    }
}
