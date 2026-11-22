<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfCore\PdfValue;

/** @internal */
class PDFValueString extends PDFValue
{
    public function __toString() : string
    {
        return '(' . \trim($this->value) . ')';
    }
}
