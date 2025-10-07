<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\Endroid\QrCode\Writer\Result;

use OCA\Libresign\Vendor\Endroid\QrCode\Matrix\MatrixInterface;
/** @internal */
final class WebPResult extends GdResult
{
    public function __construct(MatrixInterface $matrix, \GdImage $image, private readonly int $quality = -1)
    {
        parent::__construct($matrix, $image);
    }
    public function getString() : string
    {
        if (!\function_exists('imagewebp')) {
            throw new \Exception('WebP support is not available in your GD installation');
        }
        \ob_start();
        \imagewebp($this->image, quality: $this->quality);
        return \strval(\ob_get_clean());
    }
    public function getMimeType() : string
    {
        return 'image/webp';
    }
}
