<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\Endroid\QrCode\Writer;

use OCA\Libresign\Vendor\Endroid\QrCode\Label\LabelInterface;
use OCA\Libresign\Vendor\Endroid\QrCode\Logo\LogoInterface;
use OCA\Libresign\Vendor\Endroid\QrCode\QrCodeInterface;
use OCA\Libresign\Vendor\Endroid\QrCode\Writer\Result\ResultInterface;
/** @internal */
interface WriterInterface
{
    /** @param array<string, mixed> $options */
    public function write(QrCodeInterface $qrCode, ?LogoInterface $logo = null, ?LabelInterface $label = null, array $options = []) : ResultInterface;
}
