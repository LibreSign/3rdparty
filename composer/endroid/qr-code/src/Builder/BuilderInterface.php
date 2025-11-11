<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\Endroid\QrCode\Builder;

use OCA\Libresign\Vendor\Endroid\QrCode\Color\ColorInterface;
use OCA\Libresign\Vendor\Endroid\QrCode\Encoding\EncodingInterface;
use OCA\Libresign\Vendor\Endroid\QrCode\ErrorCorrectionLevel;
use OCA\Libresign\Vendor\Endroid\QrCode\Label\Font\FontInterface;
use OCA\Libresign\Vendor\Endroid\QrCode\Label\LabelAlignment;
use OCA\Libresign\Vendor\Endroid\QrCode\Label\Margin\MarginInterface;
use OCA\Libresign\Vendor\Endroid\QrCode\RoundBlockSizeMode;
use OCA\Libresign\Vendor\Endroid\QrCode\Writer\Result\ResultInterface;
use OCA\Libresign\Vendor\Endroid\QrCode\Writer\WriterInterface;
/** @internal */
interface BuilderInterface
{
    /** @param array<mixed>|null $writerOptions */
    public function build(
        ?WriterInterface $writer = null,
        ?array $writerOptions = null,
        ?bool $validateResult = null,
        // QrCode options
        ?string $data = null,
        ?EncodingInterface $encoding = null,
        ?ErrorCorrectionLevel $errorCorrectionLevel = null,
        ?int $size = null,
        ?int $margin = null,
        ?RoundBlockSizeMode $roundBlockSizeMode = null,
        ?ColorInterface $foregroundColor = null,
        ?ColorInterface $backgroundColor = null,
        // Label options
        ?string $labelText = null,
        ?FontInterface $labelFont = null,
        ?LabelAlignment $labelAlignment = null,
        ?MarginInterface $labelMargin = null,
        ?ColorInterface $labelTextColor = null,
        // Logo options
        ?string $logoPath = null,
        ?int $logoResizeToWidth = null,
        ?int $logoResizeToHeight = null,
        ?bool $logoPunchoutBackground = null
    ) : ResultInterface;
}
