<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\Endroid\QrCode\Writer;

use OCA\Libresign\Vendor\Endroid\QrCode\Label\LabelInterface;
use OCA\Libresign\Vendor\Endroid\QrCode\Logo\LogoInterface;
use OCA\Libresign\Vendor\Endroid\QrCode\QrCodeInterface;
use OCA\Libresign\Vendor\Endroid\QrCode\Writer\Result\GdResult;
use OCA\Libresign\Vendor\Endroid\QrCode\Writer\Result\GifResult;
use OCA\Libresign\Vendor\Endroid\QrCode\Writer\Result\ResultInterface;
/** @internal */
final class GifWriter extends AbstractGdWriter
{
    public function write(QrCodeInterface $qrCode, LogoInterface $logo = null, LabelInterface $label = null, array $options = []) : ResultInterface
    {
        /** @var GdResult $gdResult */
        $gdResult = parent::write($qrCode, $logo, $label, $options);
        return new GifResult($gdResult->getMatrix(), $gdResult->getImage());
    }
}
