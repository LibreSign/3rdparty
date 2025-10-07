<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\Endroid\QrCode\Label\Margin;

/** @internal */
interface MarginInterface
{
    public function getTop() : int;
    public function getRight() : int;
    public function getBottom() : int;
    public function getLeft() : int;
    /** @return array<string, int> */
    public function toArray() : array;
}
