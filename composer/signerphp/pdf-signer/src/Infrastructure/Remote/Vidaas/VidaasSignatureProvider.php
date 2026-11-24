<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Remote\Vidaas;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\ExternalSigningPayload;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\HashAlgorithm;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SignatureAlgorithm;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SignatureEncoding;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SignatureValue;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SigningInputType;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Domain\Exception\SignerException;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\RemoteSigning\Contract\RemoteSignatureProviderInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\RemoteSigning\DTO\RemoteCertificate;
/** @internal */
final readonly class VidaasSignatureProvider implements RemoteSignatureProviderInterface
{
    private const SIGNATURE_PATH = '/v0/oauth/signature';
    public function __construct(private VidaasClient $client)
    {
    }
    public function sign(ExternalSigningPayload $payload, RemoteCertificate $certificate) : SignatureValue
    {
        $this->assertSupportedPayload($payload);
        if (\trim($certificate->identifier) === '') {
            throw new SignerException('VIDaaS certificate alias cannot be empty.');
        }
        $digest = $payload->input();
        if (\strlen($digest) !== 32) {
            throw new SignerException('VIDaaS signing payload must contain a valid SHA-256 digest.');
        }
        $id = \bin2hex(\random_bytes(12));
        $json = $this->client->authorizedJson('POST', self::SIGNATURE_PATH, ['certificate_alias' => $certificate->identifier, 'hashes' => [['id' => $id, 'alias' => 'external-signature', 'hash' => $payload->inputBase64, 'hash_algorithm' => '2.16.840.1.101.3.4.2.1', 'signature_format' => 'RAW', 'padding_method' => 'PKCS1V1_5']]]);
        $this->assertCertificateIdentifier($json, $certificate->identifier);
        return $this->signature($json, $id);
    }
    private function assertSupportedPayload(ExternalSigningPayload $payload) : void
    {
        if ($payload->inputType !== SigningInputType::Digest || $payload->digestAlgorithm !== HashAlgorithm::Sha256 || $payload->signatureAlgorithm !== SignatureAlgorithm::RsaPkcs1V15 || $payload->signatureEncoding !== SignatureEncoding::RsaPkcs1) {
            throw new SignerException('VIDaaS RAW signing currently requires SHA-256 with RSA PKCS#1 v1.5.');
        }
    }
    /** @param array<string, mixed> $json */
    private function assertCertificateIdentifier(array $json, string $expected) : void
    {
        $returned = $json['certificate_alias'] ?? null;
        if ($returned !== null && (!\is_string($returned) || !\hash_equals($expected, \trim($returned)))) {
            throw new SignerException('VIDaaS signed with an unexpected certificate alias.');
        }
    }
    /** @param array<string, mixed> $json */
    private function signature(array $json, string $id) : SignatureValue
    {
        $signatures = $json['signatures'] ?? null;
        if (!\is_array($signatures)) {
            throw new SignerException('VIDaaS signature response does not contain signatures.');
        }
        foreach ($signatures as $signature) {
            if (!\is_array($signature) || ($signature['id'] ?? null) !== $id) {
                continue;
            }
            foreach (['raw_signature', 'signature', 'signature_base64'] as $field) {
                $encoded = $signature[$field] ?? null;
                $bytes = \is_string($encoded) ? \base64_decode(\preg_replace('/\\s+/', '', $encoded) ?? '', \true) : \false;
                if (\is_string($bytes) && $bytes !== '') {
                    return new SignatureValue($bytes);
                }
            }
        }
        throw new SignerException('VIDaaS signature response does not contain a valid raw signature.');
    }
}
