<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO;

/** @internal */
final readonly class SignatureActorDto
{
    public function __construct(public ?string $name = null, public ?string $contactInfo = null)
    {
    }
}
