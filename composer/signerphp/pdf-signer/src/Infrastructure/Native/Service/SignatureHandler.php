<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Service;

use OCA\Libresign\Vendor\SignerPHP\PdfCore\Metadata;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\PdfDocument;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\Service\SignatureObjectAssembler;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\SignatureAppearance;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\SignatureObject;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\CertificationLevel;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Domain\Exception\SignProcessException;
/** @internal */
class SignatureHandler
{
    /**
     * @var array{cert: string, pkey: string, extracerts?: string}
     */
    private array $certificate = ['cert' => '', 'pkey' => '', 'extracerts' => ''];
    private Metadata $metadata;
    private SignatureAppearance $appearance;
    private PdfDocument $pdfDocument;
    private string $subFilter = SignatureObject::SUBFILTER_PKCS7_DETACHED;
    private ?CertificationLevel $certificationLevel = null;
    public function __construct(private readonly SignatureObjectAssembler $signatureObjectAssembler = new SignatureObjectAssembler())
    {
        $this->appearance = SignatureAppearance::new();
    }
    public static function new(?SignatureObjectAssembler $assembler = null) : self
    {
        return new self($assembler ?? new SignatureObjectAssembler());
    }
    /**
     * @param  array{cert?: string, pkey?: string, extracerts?: mixed}  $certificate
     */
    public function withCertificate(array $certificate) : self
    {
        $this->certificate = ['cert' => (string) ($certificate['cert'] ?? ''), 'pkey' => (string) ($certificate['pkey'] ?? ''), 'extracerts' => \is_string($certificate['extracerts'] ?? null) ? $certificate['extracerts'] : ''];
        return $this;
    }
    public function withAppearance(SignatureAppearance $appearance) : self
    {
        $this->appearance = $appearance;
        return $this;
    }
    public function withoutAppearance() : self
    {
        $this->appearance->withBackgroundImage(null);
        return $this;
    }
    public function withPdfDocument(PdfDocument $pdfDocument) : self
    {
        $this->pdfDocument = $pdfDocument;
        return $this;
    }
    public function withMetadata(Metadata $metadata) : self
    {
        $this->metadata = $metadata;
        return $this;
    }
    public function hasCertificate() : bool
    {
        return !empty($this->certificate['cert']);
    }
    public function withSubFilter(string $subFilter) : self
    {
        $this->subFilter = $subFilter;
        return $this;
    }
    public function withCertificationLevel(?CertificationLevel $level) : self
    {
        $this->certificationLevel = $level;
        return $this;
    }
    public function generateSignatureInDocument() : SignatureObject
    {
        $signatureObject = $this->signatureObjectAssembler->assemble($this->requirePdfDocument(), $this->appearance, $this->requireMetadata(), $this->certificationLevel?->value);
        return $signatureObject->withSubFilter($this->subFilter);
    }
    private function requirePdfDocument() : PdfDocument
    {
        if (!isset($this->pdfDocument)) {
            throw new SignProcessException('PDF document is required to generate the signature.');
        }
        return $this->pdfDocument;
    }
    private function requireMetadata() : Metadata
    {
        if (!isset($this->metadata)) {
            throw new SignProcessException('Metadata is required to generate the signature.');
        }
        return $this->metadata;
    }
}
