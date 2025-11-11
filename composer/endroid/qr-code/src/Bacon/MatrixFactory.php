<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\Endroid\QrCode\Bacon;

use OCA\Libresign\Vendor\BaconQrCode\Encoder\Encoder;
use OCA\Libresign\Vendor\Endroid\QrCode\Matrix\Matrix;
use OCA\Libresign\Vendor\Endroid\QrCode\Matrix\MatrixFactoryInterface;
use OCA\Libresign\Vendor\Endroid\QrCode\Matrix\MatrixInterface;
use OCA\Libresign\Vendor\Endroid\QrCode\QrCodeInterface;
/** @internal */
final readonly class MatrixFactory implements MatrixFactoryInterface
{
    public function create(QrCodeInterface $qrCode) : MatrixInterface
    {
        $baconErrorCorrectionLevel = ErrorCorrectionLevelConverter::convertToBaconErrorCorrectionLevel($qrCode->getErrorCorrectionLevel());
        $baconMatrix = Encoder::encode($qrCode->getData(), $baconErrorCorrectionLevel, \strval($qrCode->getEncoding()))->getMatrix();
        $blockValues = [];
        $columnCount = $baconMatrix->getWidth();
        $rowCount = $baconMatrix->getHeight();
        for ($rowIndex = 0; $rowIndex < $rowCount; ++$rowIndex) {
            $blockValues[$rowIndex] = [];
            for ($columnIndex = 0; $columnIndex < $columnCount; ++$columnIndex) {
                $blockValues[$rowIndex][$columnIndex] = $baconMatrix->get($columnIndex, $rowIndex);
            }
        }
        return new Matrix($blockValues, $qrCode->getSize(), $qrCode->getMargin(), $qrCode->getRoundBlockSizeMode());
    }
}
