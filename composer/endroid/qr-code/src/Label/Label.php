<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\Endroid\QrCode\Label;

use OCA\Libresign\Vendor\Endroid\QrCode\Color\Color;
use OCA\Libresign\Vendor\Endroid\QrCode\Color\ColorInterface;
use OCA\Libresign\Vendor\Endroid\QrCode\Label\Font\Font;
use OCA\Libresign\Vendor\Endroid\QrCode\Label\Font\FontInterface;
use OCA\Libresign\Vendor\Endroid\QrCode\Label\Margin\Margin;
use OCA\Libresign\Vendor\Endroid\QrCode\Label\Margin\MarginInterface;
/** @internal */
final readonly class Label implements LabelInterface
{
    public function __construct(private string $text, private FontInterface $font = new Font(__DIR__ . '/../../assets/open_sans.ttf', 16), private LabelAlignment $alignment = LabelAlignment::Center, private MarginInterface $margin = new Margin(0, 10, 10, 10), private ColorInterface $textColor = new Color(0, 0, 0))
    {
    }
    public function getText() : string
    {
        return $this->text;
    }
    public function getFont() : FontInterface
    {
        return $this->font;
    }
    public function getAlignment() : LabelAlignment
    {
        return $this->alignment;
    }
    public function getMargin() : MarginInterface
    {
        return $this->margin;
    }
    public function getTextColor() : ColorInterface
    {
        return $this->textColor;
    }
}
