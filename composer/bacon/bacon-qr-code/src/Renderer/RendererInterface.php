<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\BaconQrCode\Renderer;

use OCA\Libresign\Vendor\BaconQrCode\Encoder\QrCode;
/** @internal */
interface RendererInterface
{
    public function render(QrCode $qrCode) : string;
}
