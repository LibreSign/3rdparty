<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Service\Cms;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Domain\Exception\SignProcessException;
/** @internal */
final readonly class X509Certificate
{
    public function __construct(public string $der, public string $issuerNameDer, public string $serialIntegerDer, public string $pem)
    {
    }
    public static function fromPem(string $pem) : self
    {
        $der = self::pemToDer($pem);
        [$tag, $certificate, $end] = Der::readTlv($der, 0);
        if ($tag !== 0x30 || $end !== \strlen($der)) {
            throw new SignProcessException('Signing certificate is not a DER SEQUENCE.');
        }
        [$tbsTag, $tbs] = Der::readTlv($certificate, 0);
        if ($tbsTag !== 0x30) {
            throw new SignProcessException('Signing certificate TBS is invalid.');
        }
        $offset = 0;
        [$firstTag] = Der::readTlv($tbs, $offset);
        if ($firstTag === 0xa0) {
            [, , $offset] = Der::readTlv($tbs, $offset);
        }
        [$serialTag, $serialValue, $offset] = Der::readTlv($tbs, $offset);
        if ($serialTag !== 0x2) {
            throw new SignProcessException('Signing certificate serial number is invalid.');
        }
        [, , $offset] = Der::readTlv($tbs, $offset);
        // signature algorithm
        [$issuerTag, $issuerValue] = Der::readTlv($tbs, $offset);
        if ($issuerTag !== 0x30) {
            throw new SignProcessException('Signing certificate issuer is invalid.');
        }
        return new self(der: $der, issuerNameDer: Der::tlv(0x30, $issuerValue), serialIntegerDer: Der::tlv(0x2, $serialValue), pem: $pem);
    }
    /**
     * Normalize PKCS#12 extra certificates and supplied PEM bundles to individual
     * certificates, deduplicated by DER. Empty entries are allowed, but malformed
     * nonempty entries must never silently disappear from the signature.
     *
     * @param  list<string>  $chain
     * @return list<string>
     */
    public static function normalizeChain(array $chain) : array
    {
        $certificates = [];
        foreach ($chain as $entry) {
            if (\trim($entry, " \t\r\n") === '') {
                continue;
            }
            if (\str_starts_with($entry, "0") || !\str_contains($entry, '-----BEGIN CERTIFICATE-----')) {
                // The CMS assembler also accepts individual DER certificates.
                $der = $entry;
                $certificates[$der] ??= self::validatedChainCertificatePem($der);
                continue;
            }
            $remaining = \preg_replace_callback('/-----BEGIN CERTIFICATE-----([A-Za-z0-9+\\/=\\s]+)-----END CERTIFICATE-----/', static function (array $match) use(&$certificates) : string {
                $der = \base64_decode(\preg_replace('/\\s+/', '', $match[1]) ?? '', \true);
                if ($der === \false || $der === '') {
                    throw new SignProcessException('Invalid certificate in CMS certificate chain.');
                }
                $certificates[$der] ??= self::validatedChainCertificatePem($der);
                return '';
            }, $entry);
            if ($remaining === null || \trim($remaining, " \t\r\n") !== '') {
                throw new SignProcessException('Invalid certificate in CMS certificate chain.');
            }
        }
        return \array_values($certificates);
    }
    private static function validatedChainCertificatePem(string $der) : string
    {
        $pem = "-----BEGIN CERTIFICATE-----\n" . \chunk_split(\base64_encode($der), 64, "\n") . "-----END CERTIFICATE-----\n";
        // A malformed certificate is an expected input error. Use the same scoped
        // error handling as the external signing certificate validator.
        \set_error_handler(static fn(): bool => \true);
        try {
            $certificate = \openssl_x509_read($pem);
        } finally {
            \restore_error_handler();
        }
        if ($certificate === \false) {
            throw new SignProcessException('Invalid certificate in CMS certificate chain.');
        }
        // OpenSSL can read a certificate followed by trailing bytes. Require exactly
        // one DER object, as the CMS assembler embeds the complete byte sequence.
        try {
            self::fromPem($pem);
        } catch (SignProcessException $exception) {
            throw new SignProcessException('Invalid certificate in CMS certificate chain.', previous: $exception);
        }
        return $pem;
    }
    public function keyType() : string
    {
        $parsed = \openssl_pkey_get_details(\openssl_pkey_get_public($this->pem) ?: throw new SignProcessException('Could not read signing certificate public key.'));
        $type = $parsed['type'] ?? null;
        return match ($type) {
            \OPENSSL_KEYTYPE_RSA => 'rsa',
            \OPENSSL_KEYTYPE_EC => 'ec',
            default => throw new SignProcessException('Unsupported signing certificate key type.'),
        };
    }
    private static function pemToDer(string $pem) : string
    {
        if (!\str_contains($pem, 'BEGIN')) {
            return $pem;
        }
        $body = \preg_replace('/-----[^-]+-----/', '', $pem) ?? '';
        $decoded = \base64_decode(\preg_replace('/\\s+/', '', $body) ?? '', \true);
        if ($decoded === \false || $decoded === '') {
            throw new SignProcessException('Could not decode signing certificate PEM.');
        }
        return $decoded;
    }
}
