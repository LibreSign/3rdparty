<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Service\Inspect;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract\ProcessRunnerInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Service\ShellProcessRunner;
/**
 * Extracts CMS SignerInfo metadata (digest algorithm, signature algorithm,
 * and authenticated-attribute signing time) from a PKCS#7/CMS DER blob.
 *
 * @return array{digest_algorithm:string|null,signature_algorithm:string|null,cms_signing_time:string|null}
 * @internal
 */
final class OpenSslCmsSignerInfoInspector
{
    public function __construct(private readonly ProcessRunnerInterface $processRunner = new ShellProcessRunner())
    {
    }
    /**
     * @return array{digest_algorithm:string|null,signature_algorithm:string|null,cms_signing_time:string|null}
     */
    public function inspect(string $signatureHex) : array
    {
        $empty = ['digest_algorithm' => null, 'signature_algorithm' => null, 'cms_signing_time' => null];
        $hex = \strtoupper(\preg_replace('/\\s+/', '', $signatureHex) ?? '');
        if ($hex === '' || \preg_match('/\\A[0]+\\z/', $hex) === 1) {
            return $empty;
        }
        $hex = \preg_replace('/(?:00)+$/', '', $hex) ?? $hex;
        if ($hex === '' || \strlen($hex) % 2 !== 0 || !\ctype_xdigit($hex)) {
            return $empty;
        }
        $der = \hex2bin($hex);
        if ($der === \false || $der === '') {
            return $empty;
        }
        $tmpDir = \sys_get_temp_dir();
        $cmsFile = \tempnam($tmpDir, 'pdfsig-cmsinsp');
        if ($cmsFile === \false) {
            return $empty;
        }
        \file_put_contents($cmsFile, $der);
        try {
            $command = \sprintf('openssl cms -cmsout -print -noout -inform DER -in %s', \escapeshellarg($cmsFile));
            $result = $this->processRunner->run($command);
            if (!$result->succeeded()) {
                return $empty;
            }
            return $this->parseSignerInfo($result->outputAsString());
        } finally {
            @\unlink($cmsFile);
        }
    }
    /**
     * @return array{digest_algorithm:string|null,signature_algorithm:string|null,cms_signing_time:string|null}
     */
    private function parseSignerInfo(string $output) : array
    {
        $digestAlgorithm = null;
        $signatureAlgorithm = null;
        $cmsSigningTime = null;
        // Scope parsing to the signerInfos block only, to avoid matching
        // the top-level digestAlgorithms or certificate algorithm fields.
        $signerInfosPos = \strpos($output, 'signerInfos:');
        if ($signerInfosPos === \false) {
            return ['digest_algorithm' => null, 'signature_algorithm' => null, 'cms_signing_time' => null];
        }
        $signerInfosBlock = \substr($output, $signerInfosPos);
        // digestAlgorithm: \n  algorithm: NAME (OID)
        if (\preg_match('/digestAlgorithm:\\s*\\n\\s+algorithm:\\s+(\\S+)\\s/', $signerInfosBlock, $m) === 1) {
            $digestAlgorithm = $m[1];
        }
        // signatureAlgorithm: \n  algorithm: NAME (OID)
        if (\preg_match('/signatureAlgorithm:\\s*\\n\\s+algorithm:\\s+(\\S+)\\s/', $signerInfosBlock, $m) === 1) {
            $signatureAlgorithm = $m[1];
        }
        // object: signingTime ... \n set: \n  UTCTIME:... or GENERALIZEDTIME:...
        if (\preg_match('/object:\\s+signingTime[^\\n]*\\n\\s+set:\\s*\\n\\s+(?:UTCTIME|GENERALIZEDTIME):(.+)/', $signerInfosBlock, $m) === 1) {
            $cmsSigningTime = \trim($m[1]);
        }
        return ['digest_algorithm' => $digestAlgorithm, 'signature_algorithm' => $signatureAlgorithm, 'cms_signing_time' => $cmsSigningTime];
    }
}
