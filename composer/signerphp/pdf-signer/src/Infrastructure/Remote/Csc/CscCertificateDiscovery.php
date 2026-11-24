<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Remote\Csc;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SigningCertificateDto;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Domain\Exception\SignerException;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\RemoteSigning\Contract\RemoteCertificateDiscoveryInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\RemoteSigning\DTO\RemoteCertificate;
/** @internal */
final readonly class CscCertificateDiscovery implements RemoteCertificateDiscoveryInterface
{
    public const SHA256_RSA_OID = '1.2.840.113549.1.1.11';
    public function __construct(private CscClient $client)
    {
    }
    public function certificates(?string $identifier = null) : array
    {
        $this->client->assertVersion();
        return \array_map(fn(string $id): RemoteCertificate => $this->discoverCredential($id), $this->credentialIds($identifier));
    }
    /** @return list<string> */
    private function credentialIds(?string $identifier) : array
    {
        if ($identifier !== null) {
            return [$this->requireCredentialId($identifier)];
        }
        $ids = $this->client->post('credentials/list')['credentialIDs'] ?? null;
        if (!\is_array($ids) || !\array_is_list($ids)) {
            throw new SignerException('CSC returned an invalid credential list.');
        }
        return \array_map($this->requireCredentialId(...), $ids);
    }
    private function requireCredentialId(mixed $identifier) : string
    {
        if (!\is_string($identifier) || \trim($identifier) === '') {
            throw new SignerException('CSC credential ID cannot be empty or invalid.');
        }
        return $identifier;
    }
    private function discoverCredential(string $id) : RemoteCertificate
    {
        $info = $this->client->post('credentials/info', ['credentialID' => $id, 'certificates' => 'chain']);
        $this->assertSupportedKey($info);
        $certificates = $this->certificateChain($info);
        $key = \openssl_pkey_get_public($certificates[0]);
        $details = $key === \false ? \false : \openssl_pkey_get_details($key);
        if (!\is_array($details) || $details['type'] !== \OPENSSL_KEYTYPE_RSA) {
            throw new SignerException('CSC certificate must contain an RSA public key.');
        }
        return new RemoteCertificate($id, new SigningCertificateDto($certificates[0], \array_slice($certificates, 1)));
    }
    /** @param array<string, mixed> $info */
    private function assertSupportedKey(array $info) : void
    {
        $key = $info['key'] ?? null;
        $algorithms = \is_array($key) ? $key['algo'] ?? null : null;
        if (!\is_array($key) || ($key['status'] ?? null) !== 'enabled' || !\is_array($algorithms) || !\array_is_list($algorithms) || !\in_array(self::SHA256_RSA_OID, $algorithms, \true)) {
            throw new SignerException('CSC credential cannot sign SHA-256 RSA PKCS#1 v1.5.');
        }
    }
    /**
     * @param  array<string, mixed>  $info
     * @return non-empty-list<string>
     */
    private function certificateChain(array $info) : array
    {
        $encoded = $info['cert']['certificates'] ?? null;
        if (!\is_array($encoded) || !\array_is_list($encoded) || $encoded === []) {
            throw new SignerException('CSC credential has no certificate.');
        }
        return \array_map(self::decodeCertificate(...), $encoded);
    }
    private static function decodeCertificate(mixed $encoded) : string
    {
        $der = \is_string($encoded) ? \base64_decode($encoded, \true) : \false;
        if (!\is_string($der) || $der === '') {
            throw new SignerException('CSC returned malformed certificate data.');
        }
        $pem = "-----BEGIN CERTIFICATE-----\n" . \chunk_split(\base64_encode($der), 64, "\n") . "-----END CERTIFICATE-----\n";
        \set_error_handler(static fn(): bool => \true);
        try {
            $valid = \openssl_x509_read($pem) !== \false;
        } finally {
            \restore_error_handler();
        }
        if (!$valid) {
            throw new SignerException('CSC returned an invalid X.509 certificate.');
        }
        return $pem;
    }
}
