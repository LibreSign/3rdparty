<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfCore\Service;

use OCA\Libresign\Vendor\SignerPHP\PdfCore\Buffer;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\PdfDocument;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\Xref\Xref;
/** @internal */
final class IncrementalUpdateWriter
{
    public function write(PdfDocument $document) : Buffer
    {
        [$content, $offsets] = Xref::new()->withPdfDocument($document)->generateContentToXref();
        $tail = (new XrefContentResolver())->resolve($document, $offsets, $content->size());
        return new Buffer($content->raw() . $tail->raw());
    }
}
