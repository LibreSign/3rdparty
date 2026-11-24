<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Service;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SignatureAppearanceDto;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract\DefaultSignatureAppearanceProviderInterface;
/** @internal */
final class DefaultSignatureAppearanceProvider implements DefaultSignatureAppearanceProviderInterface
{
    /**
     * Fallback 1x1 PNG pixel for environments where asset lookup fails.
     */
    private const DEFAULT_PNG_BASE64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/aX8AAAAASUVORK5CYII=';
    public function makeDefault() : SignatureAppearanceDto
    {
        return new SignatureAppearanceDto(backgroundImagePath: $this->resolveDefaultImagePath(), rect: [36, 36, 276, 120], page: 0);
    }
    private function resolveDefaultImagePath() : string
    {
        $assetPath = __DIR__ . '/../Assets/default-signature-stamp.png';
        if (\is_file($assetPath)) {
            return $assetPath;
        }
        return self::DEFAULT_PNG_BASE64;
    }
}
