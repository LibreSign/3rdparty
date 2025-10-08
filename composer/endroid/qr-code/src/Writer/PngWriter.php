<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\Endroid\QrCode\Writer;

use OCA\Libresign\Vendor\Endroid\QrCode\Label\LabelInterface;
use OCA\Libresign\Vendor\Endroid\QrCode\Logo\LogoInterface;
use OCA\Libresign\Vendor\Endroid\QrCode\QrCodeInterface;
use OCA\Libresign\Vendor\Endroid\QrCode\Writer\Result\GdResult;
use OCA\Libresign\Vendor\Endroid\QrCode\Writer\Result\PngResult;
use OCA\Libresign\Vendor\Endroid\QrCode\Writer\Result\ResultInterface;
/** @internal */
final readonly class PngWriter extends AbstractGdWriter
{
    public const WRITER_OPTION_COMPRESSION_LEVEL = 'compression_level';
    public const WRITER_OPTION_NUMBER_OF_COLORS = 'number_of_colors';
    public function write(QrCodeInterface $qrCode, ?LogoInterface $logo = null, ?LabelInterface $label = null, array $options = []) : ResultInterface
    {
        if (!isset($options[self::WRITER_OPTION_COMPRESSION_LEVEL])) {
            $options[self::WRITER_OPTION_COMPRESSION_LEVEL] = -1;
        }
        if (!\array_key_exists(self::WRITER_OPTION_NUMBER_OF_COLORS, $options)) {
            $options[self::WRITER_OPTION_NUMBER_OF_COLORS] = match (\true) {
                $qrCode->getBackgroundColor()->getAlpha() > 0 || $qrCode->getForegroundColor()->getAlpha() > 0 => null,
                $logo instanceof LogoInterface => null,
                default => 16,
            };
        }
        /** @var GdResult $gdResult */
        $gdResult = parent::write($qrCode, $logo, $label, $options);
        return new PngResult($gdResult->getMatrix(), $gdResult->getImage(), $options[self::WRITER_OPTION_COMPRESSION_LEVEL], $options[self::WRITER_OPTION_NUMBER_OF_COLORS]);
    }
}
