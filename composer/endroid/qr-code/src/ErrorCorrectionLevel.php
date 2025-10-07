<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\Endroid\QrCode;

/** @internal */
enum ErrorCorrectionLevel : string
{
    case High = 'high';
    case Low = 'low';
    case Medium = 'medium';
    case Quartile = 'quartile';
}
