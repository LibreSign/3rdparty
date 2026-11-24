<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Remote\Vidaas;

/** @internal */
final class VidaasPkce
{
    public static function generateVerifier(int $bytes = 64) : string
    {
        if ($bytes < 32 || $bytes > 72) {
            throw new \InvalidArgumentException('PKCE verifier entropy must be between 32 and 72 bytes.');
        }
        return self::base64Url(\random_bytes($bytes));
    }
    public static function challenge(string $verifier) : string
    {
        if (\strlen($verifier) < 43 || \strlen($verifier) > 128) {
            throw new \InvalidArgumentException('PKCE verifier must contain between 43 and 128 characters.');
        }
        return self::base64Url(\hash('sha256', $verifier, \true));
    }
    private static function base64Url(string $value) : string
    {
        return \rtrim(\strtr(\base64_encode($value), '+/', '-_'), '=');
    }
}
