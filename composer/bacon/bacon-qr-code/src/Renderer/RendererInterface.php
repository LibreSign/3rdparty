<?php

declare (strict_types=1);
namespace OCA\Libresign\3rdparty\BaconQrCode\Renderer;

use OCA\Libresign\3rdparty\BaconQrCode\Encoder\QrCode;
/** @internal */
interface RendererInterface
{
    public function render(QrCode $qrCode) : string;
}
