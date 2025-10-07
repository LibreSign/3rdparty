<?php

declare (strict_types=1);
namespace OCA\Libresign\3rdparty\Endroid\QrCode\Builder;

use OCA\Libresign\3rdparty\Endroid\QrCode\Color\ColorInterface;
use OCA\Libresign\3rdparty\Endroid\QrCode\Encoding\EncodingInterface;
use OCA\Libresign\3rdparty\Endroid\QrCode\ErrorCorrectionLevel;
use OCA\Libresign\3rdparty\Endroid\QrCode\Label\Font\FontInterface;
use OCA\Libresign\3rdparty\Endroid\QrCode\Label\LabelAlignment;
use OCA\Libresign\3rdparty\Endroid\QrCode\Label\Margin\MarginInterface;
use OCA\Libresign\3rdparty\Endroid\QrCode\RoundBlockSizeMode;
use OCA\Libresign\3rdparty\Endroid\QrCode\Writer\Result\ResultInterface;
use OCA\Libresign\3rdparty\Endroid\QrCode\Writer\WriterInterface;
/** @internal */
interface BuilderInterface
{
    public static function create() : BuilderInterface;
    public function writer(WriterInterface $writer) : BuilderInterface;
    /** @param array<string, mixed> $writerOptions */
    public function writerOptions(array $writerOptions) : BuilderInterface;
    public function data(string $data) : BuilderInterface;
    public function encoding(EncodingInterface $encoding) : BuilderInterface;
    public function errorCorrectionLevel(ErrorCorrectionLevel $errorCorrectionLevel) : BuilderInterface;
    public function size(int $size) : BuilderInterface;
    public function margin(int $margin) : BuilderInterface;
    public function roundBlockSizeMode(RoundBlockSizeMode $roundBlockSizeMode) : BuilderInterface;
    public function foregroundColor(ColorInterface $foregroundColor) : BuilderInterface;
    public function backgroundColor(ColorInterface $backgroundColor) : BuilderInterface;
    public function logoPath(string $logoPath) : BuilderInterface;
    public function logoResizeToWidth(int $logoResizeToWidth) : BuilderInterface;
    public function logoResizeToHeight(int $logoResizeToHeight) : BuilderInterface;
    public function logoPunchoutBackground(bool $logoPunchoutBackground) : BuilderInterface;
    public function labelText(string $labelText) : BuilderInterface;
    public function labelFont(FontInterface $labelFont) : BuilderInterface;
    public function labelAlignment(LabelAlignment $labelAlignment) : BuilderInterface;
    public function labelMargin(MarginInterface $labelMargin) : BuilderInterface;
    public function labelTextColor(ColorInterface $labelTextColor) : BuilderInterface;
    public function validateResult(bool $validateResult) : BuilderInterface;
    public function build() : ResultInterface;
}
