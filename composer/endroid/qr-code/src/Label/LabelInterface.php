<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\Endroid\QrCode\Label;

use OCA\Libresign\Vendor\Endroid\QrCode\Color\ColorInterface;
use OCA\Libresign\Vendor\Endroid\QrCode\Label\Font\FontInterface;
use OCA\Libresign\Vendor\Endroid\QrCode\Label\Margin\MarginInterface;
/** @internal */
interface LabelInterface
{
    public function getText() : string;
    public function getFont() : FontInterface;
    public function getAlignment() : LabelAlignment;
    public function getMargin() : MarginInterface;
    public function getTextColor() : ColorInterface;
}
