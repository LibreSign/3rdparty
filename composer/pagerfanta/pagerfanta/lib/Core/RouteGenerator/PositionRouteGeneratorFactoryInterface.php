<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\Pagerfanta\RouteGenerator;

use OCA\Libresign\Vendor\Pagerfanta\Exception\RuntimeException;
/**
 * Creates the route generators that views use to build the URL for each position of a paginated list.
 * @internal
 */
interface PositionRouteGeneratorFactoryInterface
{
    /**
     * @param array<string, mixed> $options
     *
     * @throws RuntimeException if the route generator cannot be created
     */
    public function createPositionRouteGenerator(array $options = []) : PositionRouteGeneratorInterface;
}
