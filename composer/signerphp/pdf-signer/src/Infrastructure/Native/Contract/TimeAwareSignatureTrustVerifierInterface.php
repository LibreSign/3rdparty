<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SignatureValidationOptionsDto;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\ValueObject\SignatureTrustVerification;
/**
 * Trust verifier able to validate a certificate chain at a given instant instead of now,
 * e.g. a TSA chain at the genTime of the timestamp it issued.
 * @internal
 */
interface TimeAwareSignatureTrustVerifierInterface extends SignatureTrustVerifierInterface
{
    public function verifyAt(string $signatureHex, SignatureValidationOptionsDto $options, \DateTimeImmutable $validationTime) : SignatureTrustVerification;
}
