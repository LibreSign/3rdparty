<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\Endroid\QrCode\Label\Font;

/** @internal */
interface FontInterface
{
    public function getPath() : string;
    public function getSize() : int;
}
