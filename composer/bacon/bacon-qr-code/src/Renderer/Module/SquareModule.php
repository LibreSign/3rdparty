<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\BaconQrCode\Renderer\Module;

use OCA\Libresign\Vendor\BaconQrCode\Encoder\ByteMatrix;
use OCA\Libresign\Vendor\BaconQrCode\Renderer\Module\EdgeIterator\EdgeIterator;
use OCA\Libresign\Vendor\BaconQrCode\Renderer\Path\Path;
/**
 * Groups modules together to a single path.
 * @internal
 */
final class SquareModule implements ModuleInterface
{
    private static ?SquareModule $instance = null;
    private function __construct()
    {
    }
    public static function instance() : self
    {
        return self::$instance ?: (self::$instance = new self());
    }
    public function createPath(ByteMatrix $matrix) : Path
    {
        $path = new Path();
        foreach (new EdgeIterator($matrix) as $edge) {
            $points = $edge->getSimplifiedPoints();
            $length = \count($points);
            $path = $path->move($points[0][0], $points[0][1]);
            for ($i = 1; $i < $length; ++$i) {
                $path = $path->line($points[$i][0], $points[$i][1]);
            }
            $path = $path->close();
        }
        return $path;
    }
}
