<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Service\Cms;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\Contract\SignatureProviderInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\HashAlgorithm;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SignatureAlgorithm;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SignaturePolicyIdentifierDto;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SignatureProfile;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SignatureValue;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SigningPayload;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Domain\Exception\SignProcessException;
/** @internal */
final class DetachedCmsAssembler
{
    private const OID_DATA = '1.2.840.113549.1.7.1';
    private const OID_SIGNED_DATA = '1.2.840.113549.1.7.2';
    private const OID_CONTENT_TYPE = '1.2.840.113549.1.9.3';
    private const OID_MESSAGE_DIGEST = '1.2.840.113549.1.9.4';
    private const OID_SIGNING_TIME = '1.2.840.113549.1.9.5';
    private const OID_SIGNING_CERTIFICATE = '1.2.840.113549.1.9.16.2.12';
    private const OID_SIGNING_CERTIFICATE_V2 = '1.2.840.113549.1.9.16.2.47';
    private const OID_SIGNATURE_POLICY_ID = '1.2.840.113549.1.9.16.2.15';
    private const OID_SPQ_ETS_URI = '1.2.840.113549.1.9.16.5.1';
    private const OID_SIGNATURE_TIMESTAMP_TOKEN = '1.2.840.113549.1.9.16.2.14';
    public function assemble(string $dataToSign, string $certificatePem, SignatureProviderInterface $signatureProvider, HashAlgorithm $algorithm = HashAlgorithm::Sha256, ?\DateTimeInterface $signingTime = null, array $certificateChainPem = [], SignatureProfile $profile = SignatureProfile::PdfBasic, ?SignaturePolicyIdentifierDto $signaturePolicy = null) : string
    {
        if ($signaturePolicy?->requiresSignatureTimestamp) {
            throw new SignProcessException(\sprintf('Signature policy %s requires a signature timestamp; use a timestamp-aware signer.', $signaturePolicy->oid));
        }
        $prepared = $this->prepare($dataToSign, $certificatePem, $algorithm, $signingTime, $certificateChainPem, $profile, $signaturePolicy);
        $signature = $signatureProvider->sign(new SigningPayload($prepared->signedAttributes, $algorithm));
        return $this->complete($prepared, $signature);
    }
    public function prepare(string $dataToSign, string $certificatePem, HashAlgorithm $algorithm = HashAlgorithm::Sha256, ?\DateTimeInterface $signingTime = null, array $certificateChainPem = [], SignatureProfile $profile = SignatureProfile::PdfBasic, ?SignaturePolicyIdentifierDto $signaturePolicy = null) : PreparedDetachedCms
    {
        if ($signaturePolicy !== null && $profile === SignatureProfile::PdfBasic) {
            throw new SignProcessException('A signature policy identifier requires a PAdES (CAdES-based) profile.');
        }
        $certificate = X509Certificate::fromPem($certificatePem);
        $digest = \hash($algorithm->value, $dataToSign, \true);
        return new PreparedDetachedCms($certificatePem, $this->signedAttributesSet($digest, $signingTime ?? new \DateTimeImmutable('now'), $certificate, $algorithm, $profile, $signaturePolicy), $algorithm, $certificate->keyType() === 'ec' ? SignatureAlgorithm::Ecdsa : SignatureAlgorithm::RsaPkcs1V15, $certificateChainPem, $signaturePolicy);
    }
    /**
     * @param  ?string  $signatureTimestampToken  DER TimeStampToken over the signature value; stored as the
     *                                            unsigned attribute id-aa-signatureTimeStampToken (RFC 3161, appendix A)
     */
    public function complete(PreparedDetachedCms $prepared, SignatureValue $signature, ?string $signatureTimestampToken = null) : string
    {
        if ($prepared->signaturePolicy?->requiresSignatureTimestamp && $signatureTimestampToken === null) {
            throw new SignProcessException(\sprintf('Signature policy %s requires a signature timestamp.', $prepared->signaturePolicy->oid));
        }
        if ($signature->bytes === '') {
            throw new SignProcessException('Signature provider returned an empty signature.');
        }
        $certificate = X509Certificate::fromPem($prepared->certificatePem);
        $algorithm = $prepared->digestAlgorithm;
        $this->assertCompatibleSignatureAlgorithm($prepared->signatureAlgorithm, $certificate->keyType());
        $digestAlgorithm = Der::algorithmIdentifier($this->digestOid($algorithm));
        $signatureAlgorithm = Der::algorithmIdentifier($this->signatureOid($prepared->signatureAlgorithm, $algorithm), $prepared->signatureAlgorithm !== SignatureAlgorithm::Ecdsa);
        $signedAttrsImplicit = "\xa0" . \substr($prepared->signedAttributes, 1);
        $signerInfo = Der::sequence(Der::tlv(0x2, "\x01") . Der::sequence($certificate->issuerNameDer . $certificate->serialIntegerDer) . $digestAlgorithm . $signedAttrsImplicit . $signatureAlgorithm . Der::octetString($signature->bytes) . ($signatureTimestampToken === null ? '' : Der::contextSpecific(1, Der::sequence(Der::objectIdentifier(self::OID_SIGNATURE_TIMESTAMP_TOKEN) . Der::set($signatureTimestampToken)))));
        $signedData = Der::sequence(Der::tlv(0x2, "\x01") . Der::set($digestAlgorithm) . Der::sequence(Der::objectIdentifier(self::OID_DATA)) . Der::contextSpecific(0, $this->certificateSet($certificate, $prepared->certificateChainPem)) . Der::set($signerInfo));
        return Der::sequence(Der::objectIdentifier(self::OID_SIGNED_DATA) . Der::contextSpecific(0, $signedData));
    }
    private function signedAttributesSet(string $messageDigest, \DateTimeInterface $signingTime, X509Certificate $certificate, HashAlgorithm $algorithm, SignatureProfile $profile, ?SignaturePolicyIdentifierDto $signaturePolicy = null) : string
    {
        $attributes = [Der::sequence(Der::objectIdentifier(self::OID_CONTENT_TYPE) . Der::set(Der::objectIdentifier(self::OID_DATA))), Der::sequence(Der::objectIdentifier(self::OID_MESSAGE_DIGEST) . Der::set(Der::octetString($messageDigest)))];
        if ($profile === SignatureProfile::PdfBasic) {
            $attributes[] = Der::sequence(Der::objectIdentifier(self::OID_SIGNING_TIME) . Der::set(Der::utcTime($signingTime)));
        } else {
            // RFC 5035 ESSCertIDv2 hashes the complete DER-encoded certificate.
            // SHA-1 instead uses the RFC 2634 ESSCertID attribute.
            $v2 = $algorithm !== HashAlgorithm::Sha1;
            $certHash = Der::octetString(\hash($algorithm->value, $certificate->der, \true));
            $issuerSerial = Der::sequence(Der::sequence(Der::contextSpecific(4, $certificate->issuerNameDer)) . $certificate->serialIntegerDer);
            $essCertId = Der::sequence(($v2 && $algorithm !== HashAlgorithm::Sha256 ? Der::algorithmIdentifier($this->digestOid($algorithm), withNullParameters: \false) : '') . $certHash . $issuerSerial);
            $attributes[] = Der::sequence(Der::objectIdentifier($v2 ? self::OID_SIGNING_CERTIFICATE_V2 : self::OID_SIGNING_CERTIFICATE) . Der::set(Der::sequence(Der::sequence($essCertId))));
        }
        if ($signaturePolicy !== null) {
            $attributes[] = $this->signaturePolicyAttribute($signaturePolicy);
        }
        \sort($attributes, \SORT_STRING);
        return Der::set(\implode('', $attributes));
    }
    /**
     * SignaturePolicyIdentifier ::= SignaturePolicyId (RFC 5126, 5.8.1):
     *   SEQUENCE { sigPolicyId OID, sigPolicyHash OtherHashAlgAndValue, sigPolicyQualifiers OPTIONAL }
     * The hash AlgorithmIdentifier omits parameters, matching the ICP-Brasil policy list.
     */
    private function signaturePolicyAttribute(SignaturePolicyIdentifierDto $policy) : string
    {
        $qualifiers = '';
        if ($policy->uri !== null) {
            $qualifiers = Der::sequence(Der::sequence(Der::objectIdentifier(self::OID_SPQ_ETS_URI) . Der::tlv(0x16, $policy->uri)));
        }
        $signaturePolicyId = Der::sequence(Der::objectIdentifier($policy->oid) . Der::sequence(Der::algorithmIdentifier($this->digestOid($policy->hashAlgorithm), withNullParameters: \false) . Der::octetString($policy->digest)) . $qualifiers);
        return Der::sequence(Der::objectIdentifier(self::OID_SIGNATURE_POLICY_ID) . Der::set($signaturePolicyId));
    }
    private function digestOid(HashAlgorithm $algorithm) : array
    {
        return match ($algorithm) {
            HashAlgorithm::Sha1 => [1, 3, 14, 3, 2, 26],
            HashAlgorithm::Sha224 => [2, 16, 840, 1, 101, 3, 4, 2, 4],
            HashAlgorithm::Sha256 => [2, 16, 840, 1, 101, 3, 4, 2, 1],
            HashAlgorithm::Sha384 => [2, 16, 840, 1, 101, 3, 4, 2, 2],
            HashAlgorithm::Sha512 => [2, 16, 840, 1, 101, 3, 4, 2, 3],
        };
    }
    private function signatureOid(SignatureAlgorithm $signatureAlgorithm, HashAlgorithm $digestAlgorithm) : string
    {
        if ($signatureAlgorithm === SignatureAlgorithm::Ecdsa) {
            return match ($digestAlgorithm) {
                HashAlgorithm::Sha1 => '1.2.840.10045.4.1',
                HashAlgorithm::Sha224 => '1.2.840.10045.4.3.1',
                HashAlgorithm::Sha256 => '1.2.840.10045.4.3.2',
                HashAlgorithm::Sha384 => '1.2.840.10045.4.3.3',
                HashAlgorithm::Sha512 => '1.2.840.10045.4.3.4',
            };
        }
        return match ($digestAlgorithm) {
            HashAlgorithm::Sha1 => '1.2.840.113549.1.1.5',
            HashAlgorithm::Sha224 => '1.2.840.113549.1.1.14',
            HashAlgorithm::Sha256 => '1.2.840.113549.1.1.11',
            HashAlgorithm::Sha384 => '1.2.840.113549.1.1.12',
            HashAlgorithm::Sha512 => '1.2.840.113549.1.1.13',
        };
    }
    private function assertCompatibleSignatureAlgorithm(SignatureAlgorithm $algorithm, string $keyType) : void
    {
        $compatible = match ($algorithm) {
            SignatureAlgorithm::Ecdsa => $keyType === 'ec',
            SignatureAlgorithm::RsaPkcs1V15 => $keyType === 'rsa',
        };
        if (!$compatible) {
            throw new SignProcessException(\sprintf('Prepared signature algorithm %s is not compatible with the %s certificate key.', $algorithm->value, $keyType));
        }
    }
    /** @param list<string> $certificateChainPem */
    private function certificateSet(X509Certificate $signer, array $certificateChainPem) : string
    {
        $certificates = [$signer->der];
        foreach ($certificateChainPem as $certificatePem) {
            $certificateDer = X509Certificate::fromPem($certificatePem)->der;
            if (!\in_array($certificateDer, $certificates, \true)) {
                $certificates[] = $certificateDer;
            }
        }
        \sort($certificates, \SORT_STRING);
        return \implode('', $certificates);
    }
}
