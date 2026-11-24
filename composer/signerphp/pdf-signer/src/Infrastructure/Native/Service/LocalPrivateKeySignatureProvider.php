<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Service;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\Contract\SignatureProviderInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\HashAlgorithm;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SignatureValue;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SigningPayload;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Domain\Exception\SignProcessException;
/** @internal */
final readonly class LocalPrivateKeySignatureProvider implements SignatureProviderInterface
{
    public function __construct(private string $privateKeyPem)
    {
    }
    public function sign(SigningPayload $payload) : SignatureValue
    {
        $privateKey = \openssl_pkey_get_private($this->privateKeyPem);
        if ($privateKey === \false) {
            throw new SignProcessException('Could not load the local private key.');
        }
        $signature = '';
        if (!\openssl_sign($payload->data, $signature, $privateKey, $this->opensslAlgorithm($payload->algorithm))) {
            throw new SignProcessException('Failed to sign payload with the local private key.');
        }
        return new SignatureValue($signature);
    }
    private function opensslAlgorithm(HashAlgorithm $algorithm) : int
    {
        return match ($algorithm) {
            HashAlgorithm::Sha1 => \OPENSSL_ALGO_SHA1,
            HashAlgorithm::Sha224 => \OPENSSL_ALGO_SHA224,
            HashAlgorithm::Sha256 => \OPENSSL_ALGO_SHA256,
            HashAlgorithm::Sha384 => \OPENSSL_ALGO_SHA384,
            HashAlgorithm::Sha512 => \OPENSSL_ALGO_SHA512,
        };
    }
}
