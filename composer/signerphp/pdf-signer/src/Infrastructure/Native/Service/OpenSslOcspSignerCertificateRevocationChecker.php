<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Service;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Domain\Exception\SignProcessException;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract\ProcessRunnerInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract\SignerCertificateRevocationCheckerInterface;
/**
 * Asks every OCSP responder listed in the signing certificate (AIA) for its status before a
 * long-term (LT/LTA) signature is produced.
 *
 * A response is only taken into account when OpenSSL verified it: its signature must chain
 * to a self-signed root from the signer's own certificate chain (a delegated responder must
 * carry id-kp-OCSPSigning), it must answer the nonce when the responder supports nonces,
 * and it must be current (thisUpdate/nextUpdate with 5 minutes of clock skew; without
 * nextUpdate, at most one day old). A verified "revoked" from any responder is fatal.
 * Unverifiable, stale or missing answers keep the previous behaviour, so offline and
 * OCSP-less certificates are not blocked.
 *
 * Why the signer's own chain is the trust anchor: this is a fail-safe for the signer's
 * certificate, not a validation of someone else's signature. The threat it defends against
 * is a tampered OCSP exchange, and a forged response cannot be signed under the root that
 * issued the signer's certificate. Whoever controls the chain can only make the check
 * inconclusive (the pre-existing behaviour) or block their own signature. Trust decisions
 * about the signature itself stay with the validators, which use their own trust store.
 * @internal
 */
final readonly class OpenSslOcspSignerCertificateRevocationChecker implements SignerCertificateRevocationCheckerInterface
{
    private const CLOCK_SKEW_SECONDS = 300;
    private const MAX_STATUS_AGE_SECONDS = 86400;
    private const OCSP_NETWORK_TIMEOUT_SECONDS = 10;
    public function __construct(private X509ExtensionUrlExtractor $urlExtractor = new X509ExtensionUrlExtractor(), private ProcessRunnerInterface $processRunner = new ShellProcessRunner())
    {
    }
    public function assertNotRevoked(string $certificatePem, array $chainPem) : void
    {
        $certificate = $certificatePem === '' ? \false : @\openssl_x509_parse($certificatePem);
        if (!\is_array($certificate)) {
            return;
        }
        $urls = $this->urlExtractor->ocspUrls($certificate);
        $issuerPem = $this->findIssuer($certificatePem, $certificate, $chainPem);
        $trustAnchors = $this->selfSignedRoots($chainPem);
        if ($urls === [] || $issuerPem === null || $trustAnchors === []) {
            return;
        }
        $files = ['cert' => $this->tempFile('ocsp-signer-cert', $certificatePem), 'issuer' => $this->tempFile('ocsp-signer-issuer', $issuerPem), 'anchors' => $this->tempFile('ocsp-trust-anchors', \implode("\n", $trustAnchors)), 'untrusted' => $this->tempFile('ocsp-chain', \implode("\n", $chainPem))];
        try {
            foreach ($urls as $url) {
                $revocation = $this->verifiedRevocation($url, $files);
                if ($revocation !== null) {
                    throw new SignProcessException(\sprintf('The signing certificate "%s" is revoked according to the OCSP responder %s%s.', $this->subject($certificate), $url, $revocation));
                }
            }
        } finally {
            foreach ($files as $file) {
                @\unlink($file);
            }
        }
    }
    /**
     * @param  array{cert:string, issuer:string, anchors:string, untrusted:string}  $files
     * @return ?string revocation details when the responder returned a verified, current "revoked"
     */
    private function verifiedRevocation(string $url, array $files) : ?string
    {
        $result = $this->processRunner->run(\sprintf('openssl ocsp -issuer %s -cert %s -url %s -CAfile %s -verify_other %s -validity_period %d -status_age %d -timeout %d', \escapeshellarg($files['issuer']), \escapeshellarg($files['cert']), \escapeshellarg($url), \escapeshellarg($files['anchors']), \escapeshellarg($files['untrusted']), self::CLOCK_SKEW_SECONDS, self::MAX_STATUS_AGE_SECONDS, self::OCSP_NETWORK_TIMEOUT_SECONDS));
        $output = $result->outputAsString();
        $verified = $result->succeeded() && \preg_match('/^Response verify OK$/mi', $output) === 1 && \preg_match('/Status times invalid/i', $output) !== 1 && \preg_match('/nonce verify error/i', $output) !== 1;
        if (!$verified || \preg_match('/:\\s*revoked\\s*$/mi', $output) !== 1) {
            return null;
        }
        $revokedAt = \preg_match('/Revocation Time:\\s*(.+)/i', $output, $m) === 1 ? ' at ' . \trim($m[1]) : '';
        $reason = \preg_match('/Reason:\\s*(.+)/i', $output, $m) === 1 ? ' (' . \trim($m[1]) . ')' : '';
        return $revokedAt . $reason;
    }
    /**
     * @param  array<string, mixed>  $certificate
     * @param  list<string>  $chainPem
     */
    private function findIssuer(string $certificatePem, array $certificate, array $chainPem) : ?string
    {
        foreach ($chainPem as $candidatePem) {
            $candidate = @\openssl_x509_parse($candidatePem);
            if (\is_array($candidate) && ($candidate['subject'] ?? null) === ($certificate['issuer'] ?? \false) && \openssl_x509_verify($certificatePem, $candidatePem) === 1) {
                return $candidatePem;
            }
        }
        return null;
    }
    /**
     * @param  list<string>  $chainPem
     * @return list<string>
     */
    private function selfSignedRoots(array $chainPem) : array
    {
        $roots = [];
        foreach ($chainPem as $candidatePem) {
            $candidate = @\openssl_x509_parse($candidatePem);
            if (\is_array($candidate) && ($candidate['subject'] ?? null) === ($candidate['issuer'] ?? \false) && \openssl_x509_verify($candidatePem, $candidatePem) === 1) {
                $roots[] = $candidatePem;
            }
        }
        return $roots;
    }
    /**
     * @param  array<string, mixed>  $certificate
     */
    private function subject(array $certificate) : string
    {
        $subject = $certificate['subject'] ?? [];
        return \is_array($subject) && isset($subject['CN']) && \is_string($subject['CN']) ? $subject['CN'] : (string) ($certificate['name'] ?? 'unknown');
    }
    private function tempFile(string $prefix, string $contents) : string
    {
        $path = \tempnam(\sys_get_temp_dir(), $prefix);
        if ($path === \false) {
            throw new SignProcessException('Could not create a temporary file for the OCSP check.');
        }
        \file_put_contents($path, $contents);
        return $path;
    }
}
