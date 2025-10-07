<?php

declare (strict_types=1);
namespace OCA\Libresign\3rdparty\Endroid\QrCode\Writer;

use OCA\Libresign\3rdparty\Endroid\QrCode\Bacon\MatrixFactory;
use OCA\Libresign\3rdparty\Endroid\QrCode\Label\LabelInterface;
use OCA\Libresign\3rdparty\Endroid\QrCode\Logo\LogoInterface;
use OCA\Libresign\3rdparty\Endroid\QrCode\QrCodeInterface;
use OCA\Libresign\3rdparty\Endroid\QrCode\Writer\Result\ConsoleResult;
use OCA\Libresign\3rdparty\Endroid\QrCode\Writer\Result\ResultInterface;
/** @internal */
final class ConsoleWriter implements WriterInterface
{
    public function write(QrCodeInterface $qrCode, LogoInterface $logo = null, LabelInterface $label = null, $options = []) : ResultInterface
    {
        $matrixFactory = new MatrixFactory();
        $matrix = $matrixFactory->create($qrCode);
        return new ConsoleResult($matrix, $qrCode->getForegroundColor(), $qrCode->getBackgroundColor());
    }
}
