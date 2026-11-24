<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Service;

use OCA\Libresign\Vendor\SignerPHP\PdfCore\Buffer;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\PdfValue\PDFValueHexString;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\PdfValue\PDFValueSimple;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\Service\DocumentTimestampObjectAssembler;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\SignatureObject;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\Xref\Xref;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO\TimestampOptionsDto;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Domain\Exception\SignProcessException;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract\DocumentTimestampApplierInterface;
use OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\Contract\TimestampTokenProviderInterface;
/** @internal */
final readonly class DocumentTimestampApplier implements DocumentTimestampApplierInterface
{
    public function __construct(private PdfDocumentPreparer $documentPreparer = new PdfDocumentPreparer(), private XrefContentResolver $xrefContentResolver = new XrefContentResolver(), private DocumentTimestampObjectAssembler $timestampAssembler = new DocumentTimestampObjectAssembler(), private TimestampTokenProviderInterface $timestampTokenProvider = new OpenSslRfc3161TimestampTokenProvider())
    {
    }
    public function apply(string $signedPdfContent, TimestampOptionsDto $options) : string
    {
        $pdfDocument = $this->documentPreparer->prepare($signedPdfContent);
        $timestamp = $this->timestampAssembler->assemble($pdfDocument);
        [$docToXref, $objectOffsets] = Xref::new()->withPdfDocument($pdfDocument)->generateContentToXref();
        $xrefOffset = $docToXref->size();
        $objectOffsets[$timestamp->getOid()] = $docToXref->size();
        $xrefOffset += \strlen($timestamp->toPdfEntry());
        $docFromXref = $this->xrefContentResolver->resolve($pdfDocument, $objectOffsets, $xrefOffset);
        $timestamp->withSizes($docToXref->size(), $docFromXref->size());
        $timestamp['Contents'] = new PDFValueSimple('');
        $signableDocument = new Buffer($docToXref->raw() . $timestamp->toPdfEntry() . $docFromXref->raw());
        $byteRange = $this->extractByteRangeValues((string) $timestamp['ByteRange']);
        $timestampHex = $this->timestampTokenProvider->requestTokenHex($signableDocument->raw(), $this->byteRangeWithinSignableDocument($byteRange, $signableDocument->size()), $options);
        $timestampHex = $this->normalizeTimestampHexSize($timestampHex);
        $timestamp['Contents'] = new PDFValueHexString($timestampHex);
        $docToXref->data($timestamp->toPdfEntry());
        return (string) new Buffer($docToXref->raw() . $docFromXref->raw());
    }
    private function normalizeTimestampHexSize(string $timestampHex) : string
    {
        $timestampHex = \strtoupper(\trim($timestampHex));
        if ($timestampHex === '') {
            throw new SignProcessException('Empty RFC3161 timestamp token.');
        }
        if (!\ctype_xdigit($timestampHex)) {
            throw new SignProcessException('RFC3161 timestamp token is not valid hex.');
        }
        $max = SignatureObject::SIGNATURE_MAX_LENGTH;
        if (\strlen($timestampHex) > $max) {
            throw new SignProcessException(\sprintf('RFC3161 token exceeds reserved signature size (%d > %d).', \strlen($timestampHex), $max));
        }
        return \str_pad($timestampHex, $max, '0');
    }
    /**
     * The signable buffer is the final document without the /Contents placeholder, so the
     * covered bytes are contiguous there: [0, length1] followed directly by [length1, length2].
     * The final document's offsets would point past the end of this buffer.
     *
     * @param  array{0:int,1:int,2:int,3:int}  $byteRange
     * @return array{0:int,1:int,2:int,3:int}
     */
    private function byteRangeWithinSignableDocument(array $byteRange, int $signableSize) : array
    {
        [, $length1, , $length2] = $byteRange;
        if ($length1 + $length2 !== $signableSize) {
            throw new SignProcessException(\sprintf('Document timestamp ByteRange covers %d bytes but the signable document has %d.', $length1 + $length2, $signableSize));
        }
        return [0, $length1, $length1, $length2];
    }
    /**
     * @return array{0:int,1:int,2:int,3:int}
     */
    private function extractByteRangeValues(string $byteRange) : array
    {
        if (\preg_match('/\\[\\s*(\\d+)\\s+(\\d+)\\s+(\\d+)\\s+(\\d+)\\s*\\]/', $byteRange, $matches) !== 1) {
            throw new SignProcessException('Could not parse ByteRange for document timestamp.');
        }
        return [(int) $matches[1], (int) $matches[2], (int) $matches[3], (int) $matches[4]];
    }
}
