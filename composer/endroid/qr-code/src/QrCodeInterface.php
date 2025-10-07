<?php

declare (strict_types=1);
namespace OCA\Libresign\3rdparty\Endroid\QrCode;

use OCA\Libresign\3rdparty\Endroid\QrCode\Color\ColorInterface;
use OCA\Libresign\3rdparty\Endroid\QrCode\Encoding\EncodingInterface;
/** @internal */
interface QrCodeInterface
{
    public function getData() : string;
    public function getEncoding() : EncodingInterface;
    public function getErrorCorrectionLevel() : ErrorCorrectionLevel;
    public function getSize() : int;
    public function getMargin() : int;
    public function getRoundBlockSizeMode() : RoundBlockSizeMode;
    public function getForegroundColor() : ColorInterface;
    public function getBackgroundColor() : ColorInterface;
}
