<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Infrastructure\Native\ValueObject;

/** @internal */
final readonly class ExtractedPdfSignature
{
    /**
     * @param  array{0:int,1:int,2:int,3:int}  $byteRange
     */
    public function __construct(public int $index, public array $byteRange, public string $signatureHex, public string $signedContent, public bool $byteRangeValid, public ?string $byteRangeError = null, public ?string $subFilter = null)
    {
    }
    /**
     * A document timestamp (/SubFilter /ETSI.RFC3161) holds an RFC 3161 TimeStampToken whose
     * TSTInfo message imprint covers the ByteRange, not a CMS signature over those bytes.
     */
    public function isDocumentTimestamp() : bool
    {
        return $this->subFilter === 'ETSI.RFC3161';
    }
}
