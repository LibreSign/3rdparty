<?php

declare (strict_types=1);
namespace OCA\Libresign\3rdparty\Endroid\QrCode;

/** @internal */
enum ErrorCorrectionLevel : string
{
    case High = 'high';
    case Low = 'low';
    case Medium = 'medium';
    case Quartile = 'quartile';
}
