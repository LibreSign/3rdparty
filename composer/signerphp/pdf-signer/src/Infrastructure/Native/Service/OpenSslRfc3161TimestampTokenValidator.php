<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Service;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Domain\Exception\SignProcessException;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract\ProcessRunnerInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract\TimestampTokenValidatorInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Service\Cms\Der;
/**
 * Validates RFC 3161 token integrity and imprint binding. CMS -noverify deliberately
 * does not establish trust in the TSA's certificate chain; policy validation must do that separately.
 * @internal
 */
final class OpenSslRfc3161TimestampTokenValidator implements TimestampTokenValidatorInterface
{
    public function __construct(private readonly ProcessRunnerInterface $processRunner = new ShellProcessRunner())
    {
    }
    public function assertValid(string $tokenDer, string $signatureValue) : void
    {
        $tokenFile = \tempnam(\sys_get_temp_dir(), 'tsa-token-');
        $infoFile = \tempnam(\sys_get_temp_dir(), 'tsa-info-');
        if ($tokenFile === \false || $infoFile === \false) {
            if (\is_string($tokenFile)) {
                @\unlink($tokenFile);
            }
            if (\is_string($infoFile)) {
                @\unlink($infoFile);
            }
            throw new SignProcessException('Could not create temporary files for signature timestamp verification.');
        }
        try {
            if (\file_put_contents($tokenFile, $tokenDer) !== \strlen($tokenDer)) {
                throw new SignProcessException('Could not write the signature timestamp token.');
            }
            $result = $this->processRunner->run('openssl cms -verify -binary -noverify -inform DER -in ' . \escapeshellarg($tokenFile) . ' -out ' . \escapeshellarg($infoFile));
            if (!$result->succeeded()) {
                throw new SignProcessException('Invalid RFC 3161 signature timestamp token (CMS signature verification failed).');
            }
            $info = \file_get_contents($infoFile);
            if (!\is_string($info) || !$this->matchesImprint($info, $signatureValue)) {
                throw new SignProcessException('Invalid RFC 3161 signature timestamp token (message imprint mismatch or invalid TSTInfo).');
            }
        } finally {
            @\unlink($tokenFile);
            @\unlink($infoFile);
        }
    }
    private function matchesImprint(string $tstInfoDer, string $signatureValue) : bool
    {
        try {
            [$tag, $tstInfo, $end] = Der::readTlv($tstInfoDer);
            if ($tag !== 0x30 || $end !== \strlen($tstInfoDer)) {
                return \false;
            }
            [$versionTag, , $offset] = Der::readTlv($tstInfo, 0);
            [$policyTag, , $offset] = Der::readTlv($tstInfo, $offset);
            [$imprintTag, $imprint] = Der::readTlv($tstInfo, $offset);
            if ($versionTag !== 0x2 || $policyTag !== 0x6 || $imprintTag !== 0x30) {
                return \false;
            }
            [$algorithmTag, $algorithm, $imprintOffset] = Der::readTlv($imprint);
            [$digestTag, $digest, $digestEnd] = Der::readTlv($imprint, $imprintOffset);
            if ($algorithmTag !== 0x30 || $digestTag !== 0x4 || $digestEnd !== \strlen($imprint)) {
                return \false;
            }
            [$oidTag, , $oidEnd] = Der::readTlv($algorithm);
            if ($oidTag !== 0x6) {
                return \false;
            }
            $oid = \substr($algorithm, 0, $oidEnd);
            $hash = match ($oid) {
                Der::objectIdentifier('1.3.14.3.2.26') => 'sha1',
                Der::objectIdentifier('2.16.840.1.101.3.4.2.4') => 'sha224',
                Der::objectIdentifier('2.16.840.1.101.3.4.2.1') => 'sha256',
                Der::objectIdentifier('2.16.840.1.101.3.4.2.2') => 'sha384',
                Der::objectIdentifier('2.16.840.1.101.3.4.2.3') => 'sha512',
                default => null,
            };
            return $hash !== null && \hash_equals(\hash($hash, $signatureValue, \true), $digest);
        } catch (\Throwable) {
            return \false;
        }
    }
}
