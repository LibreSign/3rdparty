<?php

namespace OCA\Libresign\3rdparty\Smalot\PdfParser\Encoding;

/** @internal */
class EncodingLocator
{
    protected static $encodings;
    public static function getEncoding(string $encodingClassName) : AbstractEncoding
    {
        if (!isset(self::$encodings[$encodingClassName])) {
            self::$encodings[$encodingClassName] = new $encodingClassName();
        }
        return self::$encodings[$encodingClassName];
    }
}
