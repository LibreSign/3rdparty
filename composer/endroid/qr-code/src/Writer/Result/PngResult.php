<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\Endroid\QrCode\Writer\Result;

use OCA\Libresign\Vendor\Endroid\QrCode\Matrix\MatrixInterface;
/** @internal */
final class PngResult extends GdResult
{
    public function __construct(MatrixInterface $matrix, \GdImage $image, private readonly int $quality = -1, private readonly ?int $numberOfColors = null)
    {
        parent::__construct($matrix, $image);
    }
    public function getString() : string
    {
        \ob_start();
        if (null !== $this->numberOfColors) {
            \imagetruecolortopalette($this->image, \false, $this->numberOfColors);
        }
        \imagepng($this->image, quality: $this->quality);
        return \strval(\ob_get_clean());
    }
    public function getMimeType() : string
    {
        return 'image/png';
    }
}
