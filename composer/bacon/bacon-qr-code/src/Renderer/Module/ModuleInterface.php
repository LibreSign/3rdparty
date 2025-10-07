<?php

declare (strict_types=1);
namespace OCA\Libresign\3rdparty\BaconQrCode\Renderer\Module;

use OCA\Libresign\3rdparty\BaconQrCode\Encoder\ByteMatrix;
use OCA\Libresign\3rdparty\BaconQrCode\Renderer\Path\Path;
/**
 * Interface describing how modules should be rendered.
 *
 * A module always receives a byte matrix (with values either being 1 or 0). It returns a path, where the origin
 * coordinate (0, 0) equals the top left corner of the first matrix value.
 * @internal
 */
interface ModuleInterface
{
    public function createPath(ByteMatrix $matrix) : Path;
}
