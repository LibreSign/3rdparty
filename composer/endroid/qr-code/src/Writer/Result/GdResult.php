<?php

declare (strict_types=1);
namespace OCA\Libresign\3rdparty\Endroid\QrCode\Writer\Result;

use OCA\Libresign\3rdparty\Endroid\QrCode\Matrix\MatrixInterface;
/** @internal */
class GdResult extends AbstractResult
{
    public function __construct(MatrixInterface $matrix, protected readonly \GdImage $image)
    {
        parent::__construct($matrix);
    }
    public function getImage() : \GdImage
    {
        return $this->image;
    }
    public function getString() : string
    {
        throw new \Exception('You can only use this method in a concrete implementation');
    }
    public function getMimeType() : string
    {
        throw new \Exception('You can only use this method in a concrete implementation');
    }
}
