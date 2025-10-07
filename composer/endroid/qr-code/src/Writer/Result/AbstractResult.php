<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\Endroid\QrCode\Writer\Result;

use OCA\Libresign\Vendor\Endroid\QrCode\Matrix\MatrixInterface;
/** @internal */
abstract class AbstractResult implements ResultInterface
{
    public function __construct(private readonly MatrixInterface $matrix)
    {
    }
    public function getMatrix() : MatrixInterface
    {
        return $this->matrix;
    }
    public function getDataUri() : string
    {
        return 'data:' . $this->getMimeType() . ';base64,' . \base64_encode($this->getString());
    }
    public function saveToFile(string $path) : void
    {
        $string = $this->getString();
        \file_put_contents($path, $string);
    }
}
