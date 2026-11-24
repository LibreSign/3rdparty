<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO;

/** @internal */
final readonly class ExternalSigningPayload
{
    public function __construct(public string $inputBase64, public SigningInputType $inputType, public HashAlgorithm $digestAlgorithm, public SignatureAlgorithm $signatureAlgorithm, public SignatureEncoding $signatureEncoding)
    {
    }
    public function input() : string
    {
        $input = \base64_decode($this->inputBase64, \true);
        if (!\is_string($input)) {
            throw new \InvalidArgumentException('External signing payload contains invalid base64 input.');
        }
        return $input;
    }
}
