<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\Endroid\QrCode\Bacon;

use OCA\Libresign\Vendor\BaconQrCode\Common\ErrorCorrectionLevel as BaconErrorCorrectionLevel;
use OCA\Libresign\Vendor\Endroid\QrCode\ErrorCorrectionLevel;
/** @internal */
final class ErrorCorrectionLevelConverter
{
    public static function convertToBaconErrorCorrectionLevel(ErrorCorrectionLevel $errorCorrectionLevel) : BaconErrorCorrectionLevel
    {
        return match ($errorCorrectionLevel) {
            ErrorCorrectionLevel::Low => BaconErrorCorrectionLevel::valueOf('L'),
            ErrorCorrectionLevel::Medium => BaconErrorCorrectionLevel::valueOf('M'),
            ErrorCorrectionLevel::Quartile => BaconErrorCorrectionLevel::valueOf('Q'),
            ErrorCorrectionLevel::High => BaconErrorCorrectionLevel::valueOf('H'),
        };
    }
}
