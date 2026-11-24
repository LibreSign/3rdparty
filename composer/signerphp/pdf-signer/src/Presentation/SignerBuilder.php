<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Presentation;

use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\Contract\DefaultTimestampOptionsProviderInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\BrazilSignaturePolicyOptionsDto;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\CertificateCredentialsDto;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\CertificationLevel;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\IcpBrasilPadesPolicy;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\PdfContentDto;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\ProtectionOptionsDto;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\ProtectPdfRequestDto;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SignatureAppearanceDto;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SignatureMetadataDto;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SignaturePolicyIdentifierDto;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SignatureProfile;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SigningOptionsDto;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SignPdfRequestDto;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\TimestampOptionsDto;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\Service\PdfProtectionService;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\Service\PdfSigningService;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Domain\Exception\SignerException;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Service\DefaultTimestampOptionsProvider;
/** @internal */
final class SignerBuilder
{
    private ?PdfContentDto $content = null;
    private ?CertificateCredentialsDto $certificate = null;
    private ?SignatureMetadataDto $metadata = null;
    private ?SignatureAppearanceDto $appearance = null;
    private ?TimestampOptionsDto $timestamp = null;
    private ?TimestampOptionsDto $defaultTimestamp = null;
    private ?ProtectionOptionsDto $protection = null;
    private bool $useDefaultAppearance = \true;
    private bool $useDefaultTimestamp = \false;
    private SignatureProfile $signatureProfile = SignatureProfile::PdfBasic;
    private ?CertificationLevel $certificationLevel = null;
    private ?SignaturePolicyIdentifierDto $signaturePolicy = null;
    public function __construct(private readonly PdfSigningService $service, private readonly ?PdfProtectionService $protectionService = null, private readonly DefaultTimestampOptionsProviderInterface $defaultTimestampProvider = new DefaultTimestampOptionsProvider())
    {
    }
    public static function new(PdfSigningService $service, ?PdfProtectionService $protectionService = null, ?DefaultTimestampOptionsProviderInterface $defaultTimestampProvider = null) : self
    {
        return new self($service, $protectionService, $defaultTimestampProvider ?? new DefaultTimestampOptionsProvider());
    }
    public function withPdfContent(string $content) : self
    {
        $this->content = new PdfContentDto($content);
        return $this;
    }
    public function withCertificatePath(string $path, string $password) : self
    {
        $this->certificate = CertificateCredentialsDto::fromPath($path, $password);
        return $this;
    }
    public function withCertificateContent(string $content, string $password) : self
    {
        $this->certificate = CertificateCredentialsDto::fromContent($content, $password);
        return $this;
    }
    public function withMetadata(SignatureMetadataDto $metadata) : self
    {
        $this->metadata = $metadata;
        return $this;
    }
    public function withAppearance(SignatureAppearanceDto $appearance) : self
    {
        $this->appearance = $appearance;
        return $this;
    }
    public function withoutDefaultAppearance() : self
    {
        $this->useDefaultAppearance = \false;
        return $this;
    }
    public function withTimestamp(?TimestampOptionsDto $timestamp = null) : self
    {
        $this->timestamp = $timestamp;
        $this->useDefaultTimestamp = \true;
        return $this;
    }
    public function withDefaultTimestampProfile(TimestampOptionsDto $timestamp) : self
    {
        $this->defaultTimestamp = $timestamp;
        $this->useDefaultTimestamp = \true;
        return $this;
    }
    public function withoutTimestamp() : self
    {
        $this->timestamp = null;
        $this->useDefaultTimestamp = \false;
        return $this;
    }
    public function withProtection(ProtectionOptionsDto $protection) : self
    {
        $this->protection = $protection;
        return $this;
    }
    public function withPadesBaselineB() : self
    {
        $this->signatureProfile = SignatureProfile::PadesBaselineB;
        return $this;
    }
    public function withPadesBaselineT() : self
    {
        $this->signatureProfile = SignatureProfile::PadesBaselineT;
        return $this;
    }
    public function withPadesBaselineLT() : self
    {
        $this->signatureProfile = SignatureProfile::PadesBaselineLT;
        return $this;
    }
    public function withPadesBaselineLTA() : self
    {
        $this->signatureProfile = SignatureProfile::PadesBaselineLTA;
        return $this;
    }
    /**
     * Declares an explicit signature policy (signed attribute id-aa-ets-sigPolicyId).
     * Requires a PAdES profile.
     */
    public function withSignaturePolicy(SignaturePolicyIdentifierDto|IcpBrasilPadesPolicy $policy) : self
    {
        $this->signaturePolicy = $policy instanceof IcpBrasilPadesPolicy ? $policy->toIdentifier() : $policy;
        return $this;
    }
    public function withCertificationLevel(CertificationLevel|int $level) : self
    {
        $resolved = \is_int($level) ? CertificationLevel::fromInt($level) : $level;
        if ($resolved === null) {
            throw new SignerException('Certification level must be one of: 1, 2 or 3.');
        }
        $this->certificationLevel = $resolved;
        return $this;
    }
    public function withBrazilPolicy(BrazilSignaturePolicyOptionsDto $policy) : self
    {
        $this->signatureProfile = SignatureProfile::PadesBaselineLTA;
        $this->certificationLevel = CertificationLevel::FormFillAndSignatures;
        $this->timestamp = $policy->toTimestampOptions();
        $this->signaturePolicy = $policy->signaturePolicy->toIdentifier();
        return $this;
    }
    public function protectThenSign() : string
    {
        if ($this->protection === null) {
            throw new SignerException('Protection options are required. Use withProtection().');
        }
        return $this->sign();
    }
    public function sign() : string
    {
        if ($this->content === null) {
            throw new SignerException('PDF content is required. Use withPdfContent().');
        }
        if ($this->certificate === null) {
            throw new SignerException('Certificate is required. Use withCertificatePath() or withCertificateContent().');
        }
        $pdfContent = $this->content->content;
        if ($this->protection !== null) {
            if ($this->protectionService === null) {
                throw new SignerException('Protection service is not available in this builder.');
            }
            $pdfContent = $this->protectionService->protect(new ProtectPdfRequestDto(new PdfContentDto($pdfContent), $this->protection));
        }
        $resolvedTimestamp = $this->resolveTimestamp();
        if (\in_array($this->signatureProfile, [SignatureProfile::PadesBaselineT, SignatureProfile::PadesBaselineLT, SignatureProfile::PadesBaselineLTA], \true) && $resolvedTimestamp === null) {
            throw new SignerException(\sprintf('%s requires timestamp. Use withTimestamp().', match ($this->signatureProfile) {
                SignatureProfile::PadesBaselineLT => 'PAdES Baseline-LT',
                SignatureProfile::PadesBaselineLTA => 'PAdES Baseline-LTA',
                default => 'PAdES Baseline-T',
            }));
        }
        if ($this->signaturePolicy !== null && $this->signatureProfile === SignatureProfile::PdfBasic) {
            throw new SignerException('A signature policy requires a PAdES profile (for example withPadesBaselineB()).');
        }
        if ($this->signaturePolicy?->requiresSignatureTimestamp === \true && $this->resolveTimestamp() === null) {
            throw new SignerException(\sprintf('Signature policy %s requires a signature timestamp. Use withTimestamp().', $this->signaturePolicy->oid));
        }
        $request = new SignPdfRequestDto(new PdfContentDto($pdfContent), $this->certificate, new SigningOptionsDto($this->metadata, $this->appearance, $resolvedTimestamp, $this->useDefaultAppearance, $this->signatureProfile, $this->certificationLevel, $this->signaturePolicy));
        return $this->service->sign($request);
    }
    private function resolveTimestamp() : ?TimestampOptionsDto
    {
        if ($this->timestamp !== null) {
            return $this->timestamp;
        }
        if (!$this->useDefaultTimestamp) {
            return null;
        }
        return $this->defaultTimestamp ?? $this->defaultTimestampProvider->makeDefault();
    }
}
