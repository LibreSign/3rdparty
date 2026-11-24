<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO;

/** @internal */
final readonly class SignatureMetadataDto
{
    public function __construct(public ?string $reason = null, public ?string $location = null, public ?SignatureActorDto $actor = null)
    {
    }
}
