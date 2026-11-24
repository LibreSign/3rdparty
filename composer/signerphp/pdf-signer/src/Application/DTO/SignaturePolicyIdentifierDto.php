<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Domain\Exception\SignerException;
/**
 * Explicit signature policy embedded as the signed attribute
 * id-aa-ets-sigPolicyId (RFC 5126 / ETSI EN 319 122-1, section 5.2.9).
 * @internal
 */
final readonly class SignaturePolicyIdentifierDto
{
    public HashAlgorithm $hashAlgorithm;
    /**
     * True when the policy mandates id-aa-signatureTimeStampToken in the CMS. Always true for the
     * ICP-Brasil PAdES AD-RT, AD-RC and AD-RA arcs (2.16.76.1.7.1.12/13/14), whatever the caller passed.
     */
    public bool $requiresSignatureTimestamp;
    /**
     * @param  string  $oid  dotted policy OID
     * @param  string  $digest  binary hash of the policy document (sigPolicyHash)
     * @param  ?string  $uri  optional SPURI qualifier
     * @param  bool  $requiresSignatureTimestamp  the policy mandates id-aa-signatureTimeStampToken in the CMS
     */
    public function __construct(public string $oid, public string $digest, HashAlgorithm|string $hashAlgorithm = HashAlgorithm::Sha256, public ?string $uri = null, bool $requiresSignatureTimestamp = \false)
    {
        if (\preg_match('/^[0-2](\\.(0|[1-9]\\d*))+$/', $oid) !== 1 || !self::hasValidFirstArcs($oid)) {
            throw new SignerException(\sprintf('Invalid signature policy OID "%s".', $oid));
        }
        $this->hashAlgorithm = HashAlgorithm::fromString($hashAlgorithm);
        $this->requiresSignatureTimestamp = $requiresSignatureTimestamp || \preg_match('/^2\\.16\\.76\\.1\\.7\\.1\\.1[234](\\.|$)/', $oid) === 1;
        $expectedLength = \strlen(\hash($this->hashAlgorithm->value, '', \true));
        if (\strlen($digest) !== $expectedLength) {
            throw new SignerException(\sprintf('Signature policy digest must have %d bytes for %s, got %d.', $expectedLength, $this->hashAlgorithm->value, \strlen($digest)));
        }
        if ($uri !== null && ($uri === '' || \preg_match('/^[\\x20-\\x7E]+$/', $uri) !== 1)) {
            throw new SignerException('Signature policy URI must be a non-empty ASCII string.');
        }
    }
    private static function hasValidFirstArcs(string $oid) : bool
    {
        $arcs = \explode('.', $oid);
        // ASN.1 restricts the second arc to 0..39 for the first arcs 0 and 1.
        return (int) $arcs[0] === 2 || (int) $arcs[1] < 40;
    }
    public static function fromHex(string $oid, string $digestHex, HashAlgorithm|string $hashAlgorithm = HashAlgorithm::Sha256, ?string $uri = null, bool $requiresSignatureTimestamp = \false) : self
    {
        $digest = \ctype_xdigit($digestHex) && \strlen($digestHex) % 2 === 0 ? \hex2bin($digestHex) : \false;
        if ($digest === \false) {
            throw new SignerException('Signature policy digest must be a hexadecimal string.');
        }
        return new self($oid, $digest, $hashAlgorithm, $uri, $requiresSignatureTimestamp);
    }
}
