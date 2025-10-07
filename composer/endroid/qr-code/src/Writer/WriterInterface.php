<?php

declare (strict_types=1);
namespace OCA\Libresign\3rdparty\Endroid\QrCode\Writer;

use OCA\Libresign\3rdparty\Endroid\QrCode\Label\LabelInterface;
use OCA\Libresign\3rdparty\Endroid\QrCode\Logo\LogoInterface;
use OCA\Libresign\3rdparty\Endroid\QrCode\QrCodeInterface;
use OCA\Libresign\3rdparty\Endroid\QrCode\Writer\Result\ResultInterface;
/** @internal */
interface WriterInterface
{
    /** @param array<string, mixed> $options */
    public function write(QrCodeInterface $qrCode, LogoInterface $logo = null, LabelInterface $label = null, array $options = []) : ResultInterface;
}
