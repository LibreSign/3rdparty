<?php

declare (strict_types=1);
namespace OCA\Libresign\3rdparty\Endroid\QrCode\Label\Font;

/** @internal */
final class NotoSans implements FontInterface
{
    public function __construct(private int $size = 16)
    {
    }
    public function getPath() : string
    {
        return __DIR__ . '/../../../assets/noto_sans.otf';
    }
    public function getSize() : int
    {
        return $this->size;
    }
}
