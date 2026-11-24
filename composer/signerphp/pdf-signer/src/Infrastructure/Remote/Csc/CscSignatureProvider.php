<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Remote\Csc;

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
final readonly class CscSignatureProvider implements RemoteSignatureProviderInterface
{
    public function __construct(private CscClient $client)
    {
    }
    public function sign(ExternalSigningPayload $payload, RemoteCertificate $certificate) : SignatureValue
    {
        $digest = $this->validatedDigest($payload, $certificate);
        $this->assertCredentialUnchanged($certificate);
        $sad = $this->authorize($certificate->identifier, $payload->inputBase64);
        $response = $this->client->post('signatures/signHash', ['credentialID' => $certificate->identifier, 'SAD' => $sad, 'hash' => [$payload->inputBase64], 'hashAlgo' => '2.16.840.1.101.3.4.2.1', 'signAlgo' => CscCertificateDiscovery::SHA256_RSA_OID]);
        try {
            return $this->validatedSignature($response, $certificate, $digest);
        } catch (SignerException) {
            throw new CscSignHashOutcomeUnknownException('CSC signHash response could not be verified; reconcile before resubmission.');
        }
    }
    private function validatedDigest(ExternalSigningPayload $payload, RemoteCertificate $certificate) : string
    {
        if ($payload->inputType !== SigningInputType::Digest || $payload->digestAlgorithm !== HashAlgorithm::Sha256 || $payload->signatureAlgorithm !== SignatureAlgorithm::RsaPkcs1V15 || $payload->signatureEncoding !== SignatureEncoding::RsaPkcs1) {
            throw new SignerException('CSC v1 signing requires a SHA-256 digest with RSA PKCS#1 v1.5.');
        }
        $digest = $payload->input();
        if (\strlen($digest) !== 32 || \trim($certificate->identifier) === '') {
            throw new SignerException('CSC signing requires an exact SHA-256 digest and credential ID.');
        }
        return $digest;
    }
    private function assertCredentialUnchanged(RemoteCertificate $selected) : void
    {
        $current = (new CscCertificateDiscovery($this->client))->certificates($selected->identifier)[0];
        $expected = self::certificateFingerprint($selected->certificate->certificatePem);
        $actual = self::certificateFingerprint($current->certificate->certificatePem);
        if ($expected === null || $actual === null || !\hash_equals($expected, $actual)) {
            throw new SignerException('CSC credential certificate changed after discovery.');
        }
    }
    private static function certificateFingerprint(string $pem) : ?string
    {
        \set_error_handler(static fn(): bool => \true);
        try {
            $fingerprint = \openssl_x509_fingerprint($pem, 'sha256');
        } finally {
            \restore_error_handler();
        }
        return \is_string($fingerprint) ? $fingerprint : null;
    }
    private function authorize(string $credentialId, string $digestBase64) : string
    {
        // SCAL 2 requires hash-bound SAD; also pin the digest under SCAL 1.
        $response = $this->client->post('credentials/authorize', ['credentialID' => $credentialId, 'numSignatures' => 1, 'hash' => [$digestBase64]]);
        $sad = $response['SAD'] ?? null;
        if (!\is_string($sad) || $sad === '') {
            throw new SignerException('CSC did not return a usable SAD.');
        }
        return $sad;
    }
    /** @param array<string, mixed> $response */
    private function validatedSignature(array $response, RemoteCertificate $certificate, string $digest) : SignatureValue
    {
        $signatures = $response['signatures'] ?? null;
        if (!\is_array($signatures) || !\array_is_list($signatures) || \count($signatures) !== 1) {
            throw new SignerException('CSC returned an invalid signature list.');
        }
        $signature = \is_string($signatures[0]) ? \base64_decode($signatures[0], \true) : \false;
        $publicKey = \openssl_pkey_get_public($certificate->certificate->certificatePem);
        $details = $publicKey === \false ? \false : \openssl_pkey_get_details($publicKey);
        $digestInfo = \hex2bin('3031300d060960864801650304020105000420') . $digest;
        if (!\is_string($signature) || !\is_array($details) || $details['type'] !== \OPENSSL_KEYTYPE_RSA || \strlen($signature) !== (int) \ceil($details['bits'] / 8) || !@\openssl_public_decrypt($signature, $recovered, $publicKey, \OPENSSL_PKCS1_PADDING) || !\hash_equals($digestInfo, $recovered)) {
            throw new SignerException('CSC returned a signature inconsistent with the selected certificate and digest.');
        }
        return new SignatureValue($signature);
    }
}
