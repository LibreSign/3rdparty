<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Service;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\Contract\SignatureProviderFactoryInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\Contract\SignatureProviderInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Domain\Exception\SignProcessException;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Domain\ValueObject\VerifiedCertificate;
/** @internal */
final class LocalPrivateKeySignatureProviderFactory implements SignatureProviderFactoryInterface
{
    public function create(VerifiedCertificate $certificate) : SignatureProviderInterface
    {
        $cert = (string) ($certificate->bundle['cert'] ?? '');
        $privateKey = (string) ($certificate->bundle['pkey'] ?? '');
        if ($cert === '' || $privateKey === '') {
            throw new SignProcessException('Verified certificate is missing PEM material for local signing.');
        }
        return new LocalPrivateKeySignatureProvider($privateKey);
    }
}
