<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Service;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Domain\Exception\SignatureValidationException;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract\ProcessRunnerInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract\SignatureCryptoVerifierInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Service\Cms\Der;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\ValueObject\SignatureCryptoVerification;
/**
 * Cryptographic check of a document timestamp (/SubFilter /ETSI.RFC3161, ISO 32000-2 12.8.5):
 *  1. the TimeStampToken's CMS signature over its encapsulated content is valid;
 *  2. the encapsulated content type is id-ct-TSTInfo;
 *  3. the TSTInfo message imprint equals the hash of the ByteRange bytes;
 *  4. the signer certificate carries the timeStamping extended key usage (RFC 3161, 2.3);
 *  5. the TSTInfo carries a well-formed genTime, returned as the signing time so the trust
 *     verifier can check the TSA chain at that instant.
 * Like the CMS verifier, the certificate chain is left to the trust verifier.
 * @internal
 */
final class OpenSslTimestampTokenVerifier implements SignatureCryptoVerifierInterface
{
    private const OID_TST_INFO = '1.2.840.113549.1.9.16.1.4';
    private const DIGESTS = ['1.3.14.3.2.26' => 'sha1', '2.16.840.1.101.3.4.2.4' => 'sha224', '2.16.840.1.101.3.4.2.1' => 'sha256', '2.16.840.1.101.3.4.2.2' => 'sha384', '2.16.840.1.101.3.4.2.3' => 'sha512'];
    public function __construct(private readonly ProcessRunnerInterface $processRunner = new ShellProcessRunner())
    {
    }
    public function verify(string $signedContent, string $signatureHex) : SignatureCryptoVerification
    {
        $contents = $signatureHex !== '' && \strlen($signatureHex) % 2 === 0 && \ctype_xdigit($signatureHex) ? \hex2bin($signatureHex) : \false;
        if ($contents === \false || \trim($contents, "\x00") === '') {
            return new SignatureCryptoVerification(\false, 'Empty or invalid timestamp token.');
        }
        try {
            // /Contents is zero-padded; the token ends where its outer DER length says.
            [, , $end] = Der::readTlvStrict($contents);
            $token = \substr($contents, 0, $end);
            if ($this->encapsulatedContentType($token) !== Der::objectIdentifier(self::OID_TST_INFO)) {
                return new SignatureCryptoVerification(\false, 'Document timestamp does not encapsulate an RFC 3161 TSTInfo.');
            }
        } catch (\Throwable) {
            return new SignatureCryptoVerification(\false, 'Document timestamp is not a valid CMS SignedData structure.');
        }
        $tokenFile = $this->createTempFile('pdf-ts-token');
        $tstInfoFile = $this->createTempFile('pdf-ts-tstinfo');
        $signerFile = $this->createTempFile('pdf-ts-signer');
        \file_put_contents($tokenFile, $token);
        try {
            $result = $this->processRunner->run(\sprintf('openssl cms -verify -binary -inform DER -in %s -noverify -out %s -signer %s', \escapeshellarg($tokenFile), \escapeshellarg($tstInfoFile), \escapeshellarg($signerFile)));
            if (!$result->succeeded()) {
                return new SignatureCryptoVerification(\false, 'Timestamp token signature is invalid: ' . $result->outputAsString());
            }
            $tstInfo = (string) \file_get_contents($tstInfoFile);
            $imprintError = $this->messageImprintError($tstInfo, $signedContent);
            if ($imprintError !== null) {
                return new SignatureCryptoVerification(\false, $imprintError);
            }
            $genTime = $this->genTime($tstInfo);
            if ($genTime === null) {
                return new SignatureCryptoVerification(\false, 'Timestamp token has a missing or invalid genTime.');
            }
            $signer = @\openssl_x509_parse((string) \file_get_contents($signerFile));
            // OpenSSL's timestampsign purpose requires the critical, exclusive timeStamping
            // EKU specified by RFC 3161 and checks the key usage, independently of chain trust.
            if (!\is_array($signer) || ($signer['purposes'][X509_PURPOSE_TIMESTAMP_SIGN][0] ?? \false) !== \true) {
                return new SignatureCryptoVerification(\false, 'Timestamp signer certificate does not satisfy the RFC 3161 timeStamping purpose.');
            }
            return new SignatureCryptoVerification(\true, signingTime: $genTime);
        } finally {
            @\unlink($tokenFile);
            @\unlink($tstInfoFile);
            @\unlink($signerFile);
        }
    }
    /**
     * ContentInfo { contentType, [0] SignedData { version, digestAlgorithms, encapContentInfo { eContentType, ... } } }
     */
    private function encapsulatedContentType(string $token) : string
    {
        [, $contentInfo] = Der::readTlvStrict($token);
        [, , $afterContentType] = Der::readTlvStrict($contentInfo);
        [, $explicit] = Der::readTlvStrict($contentInfo, $afterContentType);
        [, $signedData] = Der::readTlvStrict($explicit);
        [, , $afterVersion] = Der::readTlvStrict($signedData);
        [, , $afterDigestAlgorithms] = Der::readTlvStrict($signedData, $afterVersion);
        [, $encapContentInfo] = Der::readTlvStrict($signedData, $afterDigestAlgorithms);
        [$tag, $oid] = Der::readTlvStrict($encapContentInfo);
        return Der::tlv($tag, $oid);
    }
    /**
     * TSTInfo { version, policy, messageImprint { hashAlgorithm, hashedMessage }, ... }
     */
    private function messageImprintError(string $tstInfo, string $signedContent) : ?string
    {
        try {
            [, $fields] = Der::readTlvStrict($tstInfo);
            [, , $afterVersion] = Der::readTlvStrict($fields);
            [, , $afterPolicy] = Der::readTlvStrict($fields, $afterVersion);
            [, $messageImprint] = Der::readTlvStrict($fields, $afterPolicy);
            [, $algorithm, $afterAlgorithm] = Der::readTlvStrict($messageImprint);
            [$oidTag, $oid] = Der::readTlvStrict($algorithm);
            [, $hashedMessage] = Der::readTlvStrict($messageImprint, $afterAlgorithm);
        } catch (\Throwable) {
            return 'Timestamp token has a malformed TSTInfo.';
        }
        $hashAlgorithm = null;
        foreach (self::DIGESTS as $dotted => $name) {
            if (Der::objectIdentifier($dotted) === Der::tlv($oidTag, $oid)) {
                $hashAlgorithm = $name;
            }
        }
        if ($hashAlgorithm === null) {
            return 'Timestamp message imprint uses an unsupported hash algorithm.';
        }
        if (!\hash_equals($hashedMessage, \hash($hashAlgorithm, $signedContent, \true))) {
            return 'Timestamp message imprint does not match the signed ByteRange.';
        }
        return null;
    }
    /**
     * TSTInfo { version, policy, messageImprint, serialNumber, genTime GeneralizedTime, ... }.
     * RFC 3161, 2.4.2: YYYYMMDDhhmmss[.s...]Z, always UTC.
     */
    private function genTime(string $tstInfo) : ?\DateTimeImmutable
    {
        try {
            [, $fields] = Der::readTlvStrict($tstInfo);
            $offset = 0;
            for ($field = 0; $field < 4; $field++) {
                [, , $offset] = Der::readTlvStrict($fields, $offset);
            }
            [$tag, $value] = Der::readTlvStrict($fields, $offset);
        } catch (\Throwable) {
            return null;
        }
        if ($tag !== 0x18 || \preg_match('/\\A(\\d{14})(?:\\.\\d*[1-9])?Z\\z/', $value, $match) !== 1) {
            return null;
        }
        $time = \DateTimeImmutable::createFromFormat('!YmdHis', $match[1], new \DateTimeZone('UTC'));
        return $time !== \false && $time->format('YmdHis') === $match[1] ? $time : null;
    }
    private function createTempFile(string $prefix) : string
    {
        $path = \tempnam(\sys_get_temp_dir(), $prefix);
        if ($path === \false) {
            throw new SignatureValidationException('Could not create temporary files for timestamp validation.');
        }
        return $path;
    }
}
