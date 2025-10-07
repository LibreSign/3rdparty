<?php

declare (strict_types=1);
namespace OCA\Libresign\3rdparty\Endroid\QrCode\Label\Font;

/** @internal */
interface FontInterface
{
    public function getPath() : string;
    public function getSize() : int;
}
