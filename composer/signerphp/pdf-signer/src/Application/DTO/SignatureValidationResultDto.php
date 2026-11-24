<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO;

/** @internal */
final readonly class SignatureValidationResultDto
{
    /**
     * @param  array<int, SignatureValidationEntryDto>  $entries
     */
    public function __construct(public bool $hasSignatures, public bool $allValid, public array $entries)
    {
    }
}
