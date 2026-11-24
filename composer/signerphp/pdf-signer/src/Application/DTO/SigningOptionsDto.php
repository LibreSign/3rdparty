<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO;

/** @internal */
final readonly class SigningOptionsDto
{
    public function __construct(public ?SignatureMetadataDto $metadata = null, public ?SignatureAppearanceDto $appearance = null, public ?TimestampOptionsDto $timestamp = null, public bool $useDefaultAppearance = \true, public SignatureProfile $signatureProfile = SignatureProfile::PdfBasic, public ?CertificationLevel $certificationLevel = null, public ?SignaturePolicyIdentifierDto $signaturePolicy = null)
    {
    }
    public static function empty() : self
    {
        return new self();
    }
}
