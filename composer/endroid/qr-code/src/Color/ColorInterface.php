<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\Endroid\QrCode\Color;

/** @internal */
interface ColorInterface
{
    public function getRed() : int;
    public function getGreen() : int;
    public function getBlue() : int;
    public function getAlpha() : int;
    public function getOpacity() : float;
    public function getHex() : string;
    /** @return array<string, int> */
    public function toArray() : array;
}
