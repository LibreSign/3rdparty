<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\Pagerfanta\RouteGenerator;

use OCA\Libresign\Vendor\Pagerfanta\Exception\RuntimeException;
/**
 * @deprecated since Pagerfanta 4.10, implement {@see PositionRouteGeneratorFactoryInterface} instead
 * @internal
 */
interface RouteGeneratorFactoryInterface
{
    /**
     * @param array<string, mixed> $options
     *
     * @throws RuntimeException if the route generator cannot be created
     */
    public function create(array $options = []) : RouteGeneratorInterface;
}
