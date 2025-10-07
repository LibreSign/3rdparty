<?php

declare (strict_types=1);
namespace OCA\Libresign\3rdparty\Endroid\QrCode\Writer\Result;

use OCA\Libresign\3rdparty\Endroid\QrCode\Matrix\MatrixInterface;
/** @internal */
interface ResultInterface
{
    public function getMatrix() : MatrixInterface;
    public function getString() : string;
    public function getDataUri() : string;
    public function saveToFile(string $path) : void;
    public function getMimeType() : string;
}
