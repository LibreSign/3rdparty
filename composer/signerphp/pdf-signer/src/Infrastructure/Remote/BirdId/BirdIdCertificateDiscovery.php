<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Remote\BirdId;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SigningCertificateDto;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Domain\Exception\SignerException;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\RemoteSigning\Contract\RemoteCertificateDiscoveryInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\RemoteSigning\DTO\RemoteCertificate;
/** @internal */
final readonly class BirdIdCertificateDiscovery implements RemoteCertificateDiscoveryInterface
{
    private const CERTIFICATE_PATH = '/v0/oauth/certificate-discovery';
    public function __construct(private BirdIdClient $client)
    {
    }
    public function certificates(?string $identifier = null) : array
    {
        $json = $this->client->authorizedJson('GET', self::CERTIFICATE_PATH);
        $items = $json['certificates'] ?? $json;
        if (!\is_array($items)) {
            throw new SignerException('BirdID certificate discovery response is invalid.');
        }
        $wanted = $identifier !== null ? \trim($identifier) : '';
        $certificates = [];
        foreach ($items as $item) {
            $certificate = \is_array($item) ? $this->certificate($item) : null;
            if ($certificate === null) {
                continue;
            }
            if ($wanted !== '' && !\hash_equals($wanted, $certificate->identifier)) {
                continue;
            }
            $certificates[] = $certificate;
        }
        if ($certificates === []) {
            throw new SignerException('BirdID did not return any usable certificate.');
        }
        return $certificates;
    }
    /** @param array<string, mixed> $item */
    private function certificate(array $item) : ?RemoteCertificate
    {
        $alias = $item['alias'] ?? $item['certificate_alias'] ?? null;
        $certificate = $item['certificate'] ?? $item['certificatePem'] ?? null;
        if (!\is_string($alias) || \trim($alias) === '' || !\is_string($certificate) || \trim($certificate) === '') {
            return null;
        }
        return new RemoteCertificate(\trim($alias), new SigningCertificateDto($this->normalizeCertificate($certificate)));
    }
    private function normalizeCertificate(string $certificate) : string
    {
        $certificate = \trim($certificate);
        if (\str_contains($certificate, '-----BEGIN CERTIFICATE-----')) {
            return $certificate . "\n";
        }
        $der = \base64_decode(\preg_replace('/\\s+/', '', $certificate) ?? '', \true);
        if (!\is_string($der) || $der === '') {
            throw new SignerException('BirdID returned an invalid certificate.');
        }
        return "-----BEGIN CERTIFICATE-----\n" . \chunk_split(\base64_encode($der), 64, "\n") . "-----END CERTIFICATE-----\n";
    }
}
