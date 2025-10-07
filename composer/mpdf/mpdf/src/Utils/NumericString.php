<?php

namespace OCA\Libresign\Vendor\Mpdf\Utils;

/** @internal */
class NumericString
{
    public static function containsPercentChar($string)
    {
        return \strstr($string, '%');
    }
    public static function removePercentChar($string)
    {
        return \str_replace('%', '', $string);
    }
}
