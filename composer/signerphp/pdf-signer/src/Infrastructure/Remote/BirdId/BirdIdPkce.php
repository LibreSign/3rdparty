<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Remote\BirdId;

/** @internal */
final class BirdIdPkce
{
    public static function generateVerifier() : string
    {
        return \rtrim(\strtr(\base64_encode(\random_bytes(32)), '+/', '-_'), '=');
    }
    public static function challenge(#[\SensitiveParameter] string $verifier) : string
    {
        if (!\preg_match('/^[A-Za-z0-9._~-]{43,128}$/D', $verifier)) {
            throw new \InvalidArgumentException('PKCE verifier must contain 43 to 128 unreserved characters.');
        }
        return \rtrim(\strtr(\base64_encode(\hash('sha256', $verifier, \true)), '+/', '-_'), '=');
    }
}
