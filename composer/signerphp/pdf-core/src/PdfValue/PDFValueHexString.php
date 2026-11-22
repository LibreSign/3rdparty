<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfCore\PdfValue;

/** @internal */
class PDFValueHexString extends PDFValueString
{
    public function __toString() : string
    {
        return '<' . \trim((string) $this->value) . '>';
    }
}
