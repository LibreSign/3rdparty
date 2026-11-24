<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\ValueObject;

/** @internal */
final readonly class SignaturePolicyVerification
{
    public function __construct(public bool $valid, public ?string $message = null)
    {
    }
}
