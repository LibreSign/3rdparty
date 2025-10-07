<?php

declare (strict_types=1);
namespace OCA\Libresign\3rdparty\BaconQrCode\Renderer\RendererStyle;

use OCA\Libresign\3rdparty\BaconQrCode\Renderer\Color\ColorInterface;
/** @internal */
final class Gradient
{
    public function __construct(private readonly ColorInterface $startColor, private readonly ColorInterface $endColor, private readonly GradientType $type)
    {
    }
    public function getStartColor() : ColorInterface
    {
        return $this->startColor;
    }
    public function getEndColor() : ColorInterface
    {
        return $this->endColor;
    }
    public function getType() : GradientType
    {
        return $this->type;
    }
}
