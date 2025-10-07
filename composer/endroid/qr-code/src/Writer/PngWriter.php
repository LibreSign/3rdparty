<?php

declare (strict_types=1);
namespace OCA\Libresign\3rdparty\Endroid\QrCode\Writer;

use OCA\Libresign\3rdparty\Endroid\QrCode\Label\LabelInterface;
use OCA\Libresign\3rdparty\Endroid\QrCode\Logo\LogoInterface;
use OCA\Libresign\3rdparty\Endroid\QrCode\QrCodeInterface;
use OCA\Libresign\3rdparty\Endroid\QrCode\Writer\Result\GdResult;
use OCA\Libresign\3rdparty\Endroid\QrCode\Writer\Result\PngResult;
use OCA\Libresign\3rdparty\Endroid\QrCode\Writer\Result\ResultInterface;
/** @internal */
final class PngWriter extends AbstractGdWriter
{
    public const WRITER_OPTION_COMPRESSION_LEVEL = 'compression_level';
    public function write(QrCodeInterface $qrCode, LogoInterface $logo = null, LabelInterface $label = null, array $options = []) : ResultInterface
    {
        if (!isset($options[self::WRITER_OPTION_COMPRESSION_LEVEL])) {
            $options[self::WRITER_OPTION_COMPRESSION_LEVEL] = -1;
        }
        /** @var GdResult $gdResult */
        $gdResult = parent::write($qrCode, $logo, $label, $options);
        return new PngResult($gdResult->getMatrix(), $gdResult->getImage(), $options[self::WRITER_OPTION_COMPRESSION_LEVEL]);
    }
}
