<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\Endroid\QrCode\Matrix;

use OCA\Libresign\Vendor\Endroid\QrCode\QrCodeInterface;
/** @internal */
interface MatrixFactoryInterface
{
    public function create(QrCodeInterface $qrCode) : MatrixInterface;
}
