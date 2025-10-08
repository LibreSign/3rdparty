<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\Endroid\QrCode\Writer;

use OCA\Libresign\Vendor\Endroid\QrCode\Bacon\MatrixFactory;
use OCA\Libresign\Vendor\Endroid\QrCode\Label\LabelInterface;
use OCA\Libresign\Vendor\Endroid\QrCode\Logo\LogoInterface;
use OCA\Libresign\Vendor\Endroid\QrCode\QrCodeInterface;
use OCA\Libresign\Vendor\Endroid\QrCode\Writer\Result\BinaryResult;
use OCA\Libresign\Vendor\Endroid\QrCode\Writer\Result\ResultInterface;
/** @internal */
final readonly class BinaryWriter implements WriterInterface
{
    public function write(QrCodeInterface $qrCode, ?LogoInterface $logo = null, ?LabelInterface $label = null, array $options = []) : ResultInterface
    {
        $matrixFactory = new MatrixFactory();
        $matrix = $matrixFactory->create($qrCode);
        return new BinaryResult($matrix);
    }
}
