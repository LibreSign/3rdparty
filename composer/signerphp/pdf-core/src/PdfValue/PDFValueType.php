<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfCore\PdfValue;

/** @internal */
class PDFValueType extends PDFValue
{
    public function __toString() : string
    {
        return '/' . \trim((string) $this->value);
    }
}
