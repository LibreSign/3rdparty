<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Presentation;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\Service\PdfProtectionService;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\Service\PdfSignatureValidationService;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\Service\PdfSigningService;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\Service\TimestampService;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Legacy\OpenSslCertificateValidator;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\NativePdfProtectionEngine;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\NativePdfSignatureValidationEngine;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\NativePdfSigningEngine;
/** @internal */
final class Signer
{
    public static function signer() : SignerBuilder
    {
        $signingService = new PdfSigningService(new OpenSslCertificateValidator(), new NativePdfSigningEngine());
        $protectionService = new PdfProtectionService(new NativePdfProtectionEngine());
        return SignerBuilder::new($signingService, $protectionService);
    }
    public static function externalSigner() : ExternalSignerBuilder
    {
        return ExternalSignerBuilder::new();
    }
    public static function protection() : PdfProtectionBuilder
    {
        $service = new PdfProtectionService(new NativePdfProtectionEngine());
        return PdfProtectionBuilder::new($service);
    }
    public static function validation() : PdfSignatureValidatorBuilder
    {
        $service = new PdfSignatureValidationService(new NativePdfSignatureValidationEngine());
        return PdfSignatureValidatorBuilder::new($service);
    }
    public static function timestamp() : TimestampBuilder
    {
        return TimestampBuilder::new(new TimestampService());
    }
}
