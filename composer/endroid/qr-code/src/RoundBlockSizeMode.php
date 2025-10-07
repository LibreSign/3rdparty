<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\Endroid\QrCode;

/** @internal */
enum RoundBlockSizeMode : string
{
    case Enlarge = 'enlarge';
    case Margin = 'margin';
    case None = 'none';
    case Shrink = 'shrink';
}
