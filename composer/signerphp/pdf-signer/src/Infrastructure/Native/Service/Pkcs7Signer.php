<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Service;

use OCA\Libresign\Vendor\SignerPHP\PdfCore\Buffer;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\SignatureObject;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\Contract\SignatureProviderInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SignaturePolicyIdentifierDto;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SignatureProfile;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SigningPayload;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\TimestampOptionsDto;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Domain\Exception\SignProcessException;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract\ChainAwarePkcs7SignerInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract\TimestampTokenProviderInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract\TimestampTokenValidatorInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Service\Cms\DetachedCmsAssembler;
/** @internal */
final class Pkcs7Signer implements ChainAwarePkcs7SignerInterface
{
    public function __construct(private readonly DetachedCmsAssembler $cmsAssembler = new DetachedCmsAssembler(), private readonly TimestampTokenProviderInterface $timestampTokenProvider = new OpenSslRfc3161TimestampTokenProvider(), private readonly TimestampTokenValidatorInterface $timestampTokenValidator = new OpenSslRfc3161TimestampTokenValidator())
    {
    }
    public function sign(Buffer $signableDocument, SignatureProviderInterface $signatureProvider, string $certificatePem) : string
    {
        return $this->signWithProfile($signableDocument, $signatureProvider, $certificatePem, SignatureProfile::PdfBasic);
    }
    public function signWithProfile(Buffer $signableDocument, SignatureProviderInterface $signatureProvider, string $certificatePem, SignatureProfile $profile) : string
    {
        return $this->signWithChain($signableDocument, $signatureProvider, $certificatePem, $profile, []);
    }
    public function signWithPolicy(Buffer $signableDocument, SignatureProviderInterface $signatureProvider, string $certificatePem, SignatureProfile $profile, SignaturePolicyIdentifierDto $signaturePolicy, ?TimestampOptionsDto $signatureTimestamp = null) : string
    {
        return $this->signWithChain($signableDocument, $signatureProvider, $certificatePem, $profile, [], $signaturePolicy, $signatureTimestamp);
    }
    public function signWithChain(Buffer $signableDocument, SignatureProviderInterface $signatureProvider, string $certificatePem, SignatureProfile $profile, array $certificateChainPem, ?SignaturePolicyIdentifierDto $signaturePolicy = null, ?TimestampOptionsDto $signatureTimestamp = null) : string
    {
        if ($signaturePolicy === null) {
            return $this->assembleHex($signableDocument, $signatureProvider, $certificatePem, $profile, $certificateChainPem, null, null);
        }
        if ($signaturePolicy->requiresSignatureTimestamp && $signatureTimestamp === null) {
            throw new SignProcessException(\sprintf('Signature policy %s requires a signature timestamp; configure a TSA.', $signaturePolicy->oid));
        }
        return $this->assembleHex($signableDocument, $signatureProvider, $certificatePem, $profile, $certificateChainPem, $signaturePolicy, $signaturePolicy->requiresSignatureTimestamp ? $signatureTimestamp : null);
    }
    /**
     * @param  array<int, string>  $certificateChainPem
     */
    private function assembleHex(Buffer $signableDocument, SignatureProviderInterface $signatureProvider, string $certificatePem, SignatureProfile $profile, array $certificateChainPem, ?SignaturePolicyIdentifierDto $signaturePolicy, ?TimestampOptionsDto $signatureTimestamp) : string
    {
        if ($certificatePem === '') {
            throw new SignProcessException('Signing certificate PEM is required to assemble CMS.');
        }
        $prepared = $this->cmsAssembler->prepare($signableDocument->raw(), $certificatePem, certificateChainPem: $certificateChainPem, profile: $profile, signaturePolicy: $signaturePolicy);
        $signature = $signatureProvider->sign(new SigningPayload($prepared->signedAttributes, $prepared->digestAlgorithm));
        $timestampToken = null;
        if ($signatureTimestamp !== null && $signature->bytes !== '') {
            // RFC 3161 appendix A: the token's message imprint is the hash of the signature value.
            $length = \strlen($signature->bytes);
            $tokenHex = $this->timestampTokenProvider->requestTokenHex($signature->bytes, [0, $length, $length, 0], $signatureTimestamp);
            $timestampToken = \ctype_xdigit($tokenHex) && \strlen($tokenHex) % 2 === 0 ? \hex2bin($tokenHex) : \false;
            if ($timestampToken === \false || $timestampToken === '') {
                throw new SignProcessException('The TSA returned an invalid signature timestamp token.');
            }
            $this->timestampTokenValidator->assertValid($timestampToken, $signature->bytes);
        }
        $cms = $this->cmsAssembler->complete($prepared, $signature, $timestampToken);
        $hex = \bin2hex($cms);
        if (\strlen($hex) > SignatureObject::SIGNATURE_MAX_LENGTH) {
            throw new SignProcessException('CMS signature exceeds the reserved PDF signature size.');
        }
        return \str_pad($hex, SignatureObject::SIGNATURE_MAX_LENGTH, '0');
    }
}
