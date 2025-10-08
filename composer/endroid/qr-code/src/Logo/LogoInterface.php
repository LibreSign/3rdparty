<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\Endroid\QrCode\Logo;

/** @internal */
interface LogoInterface
{
    public function getPath() : string;
    public function getResizeToWidth() : ?int;
    public function getResizeToHeight() : ?int;
    public function getPunchoutBackground() : bool;
}
