<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\Contract\PdfSignatureValidationEngineInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SignatureValidationEntryDto;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SignatureValidationResultDto;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\ValidatePdfRequestDto;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Domain\Exception\SignatureValidationException;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract\BrazilPolicyListVerifierInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract\PdfSignatureExtractorInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract\SignatureCryptoVerifierInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract\SignatureTrustVerifierInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract\TimeAwareSignatureTrustVerifierInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Service\OpenSslBrazilPolicyListVerifier;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Service\OpenSslSignatureCryptoVerifier;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Service\OpenSslSignatureTrustVerifier;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Service\OpenSslTimestampTokenVerifier;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Service\PdfSignatureExtractor;
/** @internal */
final readonly class NativePdfSignatureValidationEngine implements PdfSignatureValidationEngineInterface
{
    public function __construct(private PdfSignatureExtractorInterface $signatureExtractor = new PdfSignatureExtractor(), private SignatureCryptoVerifierInterface $cryptoVerifier = new OpenSslSignatureCryptoVerifier(), private SignatureTrustVerifierInterface $trustVerifier = new OpenSslSignatureTrustVerifier(), private BrazilPolicyListVerifierInterface $policyVerifier = new OpenSslBrazilPolicyListVerifier(), private SignatureCryptoVerifierInterface $timestampVerifier = new OpenSslTimestampTokenVerifier())
    {
    }
    public function validate(ValidatePdfRequestDto $request) : SignatureValidationResultDto
    {
        try {
            $extracted = $this->signatureExtractor->extract($request->pdf->content);
            if ($extracted === []) {
                return new SignatureValidationResultDto(\false, \false, []);
            }
            $entries = [];
            $policy = $request->options->policy === 'br-iti' && $request->options->checkPolicyList ? $this->policyVerifier->verifyPadesPolicy($request->options) : null;
            foreach ($extracted as $signature) {
                $verifier = $signature->isDocumentTimestamp() ? $this->timestampVerifier : $this->cryptoVerifier;
                $crypto = $signature->byteRangeValid ? $verifier->verify($signature->signedContent, $signature->signatureHex) : null;
                $reason = $signature->byteRangeValid ? $crypto?->message : $signature->byteRangeError;
                $cryptoValid = $signature->byteRangeValid && ($crypto?->valid ?? \false);
                $trust = null;
                if ($signature->byteRangeValid && $cryptoValid) {
                    // A TSA chain is judged when the timestamp was issued (genTime), not today:
                    // the TSA certificate may have expired since. CMS signatures keep today's check.
                    $trust = $signature->isDocumentTimestamp() && $crypto?->signingTime !== null && $this->trustVerifier instanceof TimeAwareSignatureTrustVerifierInterface ? $this->trustVerifier->verifyAt($signature->signatureHex, $request->options, $crypto->signingTime) : $this->trustVerifier->verify($signature->signatureHex, $request->options);
                }
                $trustValid = $trust?->valid;
                if ($signature->byteRangeValid && $cryptoValid && $trustValid === \false) {
                    $reason = $trust?->message;
                }
                if ($request->options->policy === 'br-iti' && $signature->byteRangeValid && $cryptoValid && $trustValid !== \true) {
                    $trustValid = \false;
                    $reason = $reason ?? 'BR-ITI policy requires trusted certificate chain validation.';
                }
                $policyValid = $policy?->valid;
                if ($request->options->policy === 'br-iti' && $request->options->checkPolicyList && $policyValid !== \true) {
                    $policyValid = \false;
                    $reason = $policy?->message ?? 'BR-ITI policy list verification failed.';
                }
                $valid = $signature->byteRangeValid && $cryptoValid && ($trustValid ?? \true) && ($policyValid ?? \true);
                $entries[] = new SignatureValidationEntryDto(index: $signature->index, byteRange: $signature->byteRange, byteRangeValid: $signature->byteRangeValid, cryptoValid: $cryptoValid, trustValid: $trustValid, policyValid: $policyValid, valid: $valid, reason: $reason);
            }
            $allValid = \true;
            foreach ($entries as $entry) {
                if (!$entry->valid) {
                    $allValid = \false;
                    break;
                }
            }
            return new SignatureValidationResultDto(\true, $allValid, $entries);
        } catch (\Throwable $throwable) {
            throw new SignatureValidationException(\sprintf('Could not validate PDF signatures using native v1 engine. Root cause: %s', $throwable->getMessage()), previous: $throwable);
        }
    }
}
