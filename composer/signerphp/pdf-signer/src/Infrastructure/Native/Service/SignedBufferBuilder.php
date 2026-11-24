<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Service;

use OCA\Libresign\Vendor\SignerPHP\PdfCore\Buffer;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\PdfDocument;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\PdfValue\PDFValueHexString;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\PdfValue\PDFValueSimple;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\Xref\Xref;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\Contract\SignatureProviderInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SignatureProfile;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\SigningContextDto;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract\ChainAwarePkcs7SignerInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract\DocumentTimestampApplierInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract\LongTermValidationApplierInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract\Pkcs7SignerInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract\PolicyAwarePkcs7SignerInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract\ProfileAwarePkcs7SignerInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract\SignedBufferBuilderInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract\SignerCertificateRevocationCheckerInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract\XrefContentResolverInterface;
/** @internal */
final readonly class SignedBufferBuilder implements SignedBufferBuilderInterface
{
    public function __construct(private XrefContentResolverInterface $xrefContentResolver, private Pkcs7SignerInterface $pkcs7Signer, private DocumentTimestampApplierInterface $documentTimestampApplier = new DocumentTimestampApplier(), private LongTermValidationApplierInterface $longTermValidationApplier = new DocumentLongTermValidationApplier(), private SignerCertificateRevocationCheckerInterface $revocationChecker = new OpenSslOcspSignerCertificateRevocationChecker())
    {
    }
    public function build(PdfDocument $pdfDocument, SignatureHandler $signatureHandler, SigningContextDto $context, SignatureProviderInterface $signatureProvider) : Buffer
    {
        if (!$signatureHandler->hasCertificate()) {
            return $pdfDocument->getBuffer();
        }
        // LT/LTA embed revocation data for the signer; never produce them for a revoked certificate.
        if (\in_array($context->options->signatureProfile, [SignatureProfile::PadesBaselineLT, SignatureProfile::PadesBaselineLTA], \true)) {
            $this->revocationChecker->assertNotRevoked($context->certificate->certificatePem, $context->certificate->chainPem);
        }
        $pdfDocument->updateModifyDate();
        $signature = $signatureHandler->generateSignatureInDocument();
        [$docToXref, $objectOffsets] = Xref::new()->withPdfDocument($pdfDocument)->generateContentToXref();
        $xrefOffset = $docToXref->size();
        $objectOffsets[$signature->getOid()] = $docToXref->size();
        $xrefOffset += \strlen($signature->toPdfEntry());
        $docFromXref = $this->xrefContentResolver->resolve($pdfDocument, $objectOffsets, $xrefOffset);
        $signature->withSizes($docToXref->size(), $docFromXref->size());
        $signature['Contents'] = new PDFValueSimple('');
        $signableDocument = new Buffer($docToXref->raw() . $signature->toPdfEntry() . $docFromXref->raw());
        $certificatePem = $context->certificate->certificatePem;
        $profile = $context->options->signatureProfile;
        $certificateChainPem = $context->certificate->chainPem;
        $signaturePolicy = $context->options->signaturePolicy;
        if ($this->pkcs7Signer instanceof ChainAwarePkcs7SignerInterface && $certificateChainPem !== []) {
            $signatureContents = $this->pkcs7Signer->signWithChain($signableDocument, $signatureProvider, $certificatePem, $profile, $certificateChainPem, $signaturePolicy, $signaturePolicy !== null ? $context->options->timestamp : null);
        } elseif ($signaturePolicy !== null) {
            if (!$this->pkcs7Signer instanceof PolicyAwarePkcs7SignerInterface) {
                throw new \LogicException('A signature policy requires a policy-aware CMS signer.');
            }
            $signatureContents = $this->pkcs7Signer->signWithPolicy($signableDocument, $signatureProvider, $certificatePem, $profile, $signaturePolicy, $context->options->timestamp);
        } elseif ($this->pkcs7Signer instanceof ProfileAwarePkcs7SignerInterface) {
            $signatureContents = $this->pkcs7Signer->signWithProfile($signableDocument, $signatureProvider, $certificatePem, $profile);
        } elseif ($profile === SignatureProfile::PdfBasic) {
            $signatureContents = $this->pkcs7Signer->sign($signableDocument, $signatureProvider, $certificatePem);
        } else {
            throw new \LogicException('PAdES signing requires a profile-aware CMS signer.');
        }
        $signature['Contents'] = new PDFValueHexString($signatureContents);
        $docToXref->data($signature->toPdfEntry());
        $signedRaw = $docToXref->raw() . $docFromXref->raw();
        $timestamp = $context->options->timestamp;
        if ($timestamp !== null) {
            $signedRaw = $this->documentTimestampApplier->apply($signedRaw, $timestamp);
        }
        if (\in_array($context->options->signatureProfile, [SignatureProfile::PadesBaselineLT, SignatureProfile::PadesBaselineLTA], \true)) {
            $signedRaw = $this->longTermValidationApplier->apply($signedRaw);
        }
        if ($context->options->signatureProfile === SignatureProfile::PadesBaselineLTA && $timestamp !== null) {
            $signedRaw = $this->documentTimestampApplier->apply($signedRaw, $timestamp);
        }
        return new Buffer($signedRaw);
    }
}
