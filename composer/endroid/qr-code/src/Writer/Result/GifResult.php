<?php

declare (strict_types=1);
namespace OCA\Libresign\3rdparty\Endroid\QrCode\Writer\Result;

/** @internal */
final class GifResult extends GdResult
{
    public function getString() : string
    {
        \ob_start();
        \imagegif($this->image);
        return \strval(\ob_get_clean());
    }
    public function getMimeType() : string
    {
        return 'image/gif';
    }
}
