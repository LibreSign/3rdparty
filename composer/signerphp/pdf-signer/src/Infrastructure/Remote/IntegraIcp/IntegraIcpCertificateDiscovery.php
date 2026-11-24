<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Remote\IntegraIcp;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SigningCertificateDto;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Domain\Exception\SignerException;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\RemoteSigning\Contract\RemoteCertificateDiscoveryInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\RemoteSigning\DTO\RemoteCertificate;
/** @internal */
final readonly class IntegraIcpCertificateDiscovery implements RemoteCertificateDiscoveryInterface
{
    public function __construct(private IntegraIcpClient $client, private string $credentialId, #[\SensitiveParameter] private string $codeVerifier)
    {
        if (\trim($credentialId) === '') {
            throw new \InvalidArgumentException('IntegraICP credential ID cannot be empty.');
        }
        IntegraIcpPkce::challenge($codeVerifier);
    }
    public function certificates(?string $identifier = null) : array
    {
        if ($identifier !== null && $identifier !== $this->credentialId) {
            throw new SignerException('IntegraICP certificate identifier must match the authorized credential.');
        }
        $data = $this->client->json('GET', 'credentials/' . \rawurlencode($this->credentialId), query: ['secret_data' => $this->codeVerifier, 'secret_type' => 'code_verifier']);
        if (($data['credentialId'] ?? null) !== $this->credentialId) {
            throw new SignerException('IntegraICP returned an unexpected credential ID.');
        }
        $pem = $data['certificateInformation']['encodedX509'] ?? null;
        if (!\is_string($pem) || !\str_contains($pem, '-----BEGIN CERTIFICATE-----') || \openssl_x509_read($pem) === \false) {
            throw new SignerException('IntegraICP returned an invalid X509 certificate.');
        }
        return [new RemoteCertificate($this->credentialId, new SigningCertificateDto(\trim($pem) . "\n"))];
    }
}
