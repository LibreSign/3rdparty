<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Service;

use OCA\Libresign\Vendor\SignerPHP\PdfCore\Metadata;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\PdfDocument;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\SignatureAppearance;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\SignatureObject;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SignatureAppearanceDto;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SignatureProfile;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SigningContextDto;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract\DefaultSignatureAppearanceProviderInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract\SignatureFactoryInterface;
/** @internal */
final class PdfSignatureFactory implements SignatureFactoryInterface
{
    public function __construct(private readonly DefaultSignatureAppearanceProviderInterface $defaultAppearanceProvider = new DefaultSignatureAppearanceProvider())
    {
    }
    public function create(SigningContextDto $context, PdfDocument $pdfDocument) : SignatureHandler
    {
        $signature = SignatureHandler::new()->withPdfDocument($pdfDocument)->withCertificate(['cert' => $context->certificate->certificatePem, 'extracerts' => $context->certificate->chainPem])->withMetadata($this->toMetadata($context))->withSubFilter($this->resolveSubFilter($context))->withCertificationLevel($context->options->certificationLevel);
        $appearance = $this->resolveAppearance($context);
        if ($appearance !== null) {
            $signature->withAppearance(SignatureAppearance::new()->withBackgroundImage($appearance->backgroundImagePath)->withXObject($appearance->xObject)->withSignatureImage($appearance->signatureImagePath)->withSignatureImageFrame($appearance->signatureImageFrame)->withRect($appearance->normalizedRect())->addSignAppearanceInPage($appearance->page));
        }
        return $signature;
    }
    private function resolveAppearance(SigningContextDto $context) : ?SignatureAppearanceDto
    {
        $appearance = $context->options->appearance;
        if ($appearance !== null) {
            return $appearance;
        }
        if (!$context->options->useDefaultAppearance) {
            return null;
        }
        return $this->defaultAppearanceProvider->makeDefault();
    }
    private function toMetadata(SigningContextDto $context) : Metadata
    {
        $metadata = $context->options->metadata;
        return Metadata::new()->withName($metadata?->actor?->name)->withReason($metadata?->reason)->withLocation($metadata?->location)->withContactInfo($metadata?->actor?->contactInfo);
    }
    private function resolveSubFilter(SigningContextDto $context) : string
    {
        return match ($context->options->signatureProfile) {
            SignatureProfile::PadesBaselineB, SignatureProfile::PadesBaselineT, SignatureProfile::PadesBaselineLT, SignatureProfile::PadesBaselineLTA => SignatureObject::SUBFILTER_ETSI_CADES_DETACHED,
            default => SignatureObject::SUBFILTER_PKCS7_DETACHED,
        };
    }
}
