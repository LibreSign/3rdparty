<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\Pagerfanta\RouteGenerator;

use OCA\Libresign\Vendor\Pagerfanta\Exception\InvalidArgumentException;
use OCA\Libresign\Vendor\Pagerfanta\Position\Position;
/**
 * Generates the URL for a position in a paginated list, independent of the pagination strategy.
 * @internal
 */
interface PositionRouteGeneratorInterface
{
    /**
     * @return string The URL for the position
     *
     * @throws InvalidArgumentException if the position is not supported by this generator
     */
    public function __invoke(Position $position) : string;
}
