<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\Contract;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\CertificateCredentialsDto;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Domain\ValueObject\VerifiedCertificate;
/** @internal */
interface CertificateValidatorInterface
{
    public function validate(CertificateCredentialsDto $credentials) : VerifiedCertificate;
}
