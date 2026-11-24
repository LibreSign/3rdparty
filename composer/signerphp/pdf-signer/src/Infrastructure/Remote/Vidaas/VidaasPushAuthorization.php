<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Remote\Vidaas;

/** @internal */
final readonly class VidaasPushAuthorization
{
    public function __construct(public string $code)
    {
    }
}
