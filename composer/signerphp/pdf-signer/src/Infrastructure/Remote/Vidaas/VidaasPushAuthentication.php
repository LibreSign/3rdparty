<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Remote\Vidaas;

/** @internal */
final readonly class VidaasPushAuthentication
{
    private function __construct(public bool $approved, public ?string $authorizationToken = null, public ?string $redirectUrl = null)
    {
    }
    public static function pending() : self
    {
        return new self(\false);
    }
    public static function approved(string $authorizationToken, ?string $redirectUrl = null) : self
    {
        return new self(\true, $authorizationToken, $redirectUrl);
    }
}
