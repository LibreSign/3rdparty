<?php

declare (strict_types=1);
namespace OCA\Libresign\3rdparty\Endroid\QrCode\Encoding;

/** @internal */
interface EncodingInterface extends \Stringable
{
    public function __toString() : string;
}
