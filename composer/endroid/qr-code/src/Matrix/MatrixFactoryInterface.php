<?php

declare (strict_types=1);
namespace OCA\Libresign\3rdparty\Endroid\QrCode\Matrix;

use OCA\Libresign\3rdparty\Endroid\QrCode\QrCodeInterface;
/** @internal */
interface MatrixFactoryInterface
{
    public function create(QrCodeInterface $qrCode) : MatrixInterface;
}
