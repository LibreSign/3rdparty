<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\Pagerfanta\RouteGenerator;

use OCA\Libresign\Vendor\Pagerfanta\Exception\RuntimeException;
/** @internal */
interface RouteGeneratorFactoryInterface
{
    /**
     * @param array<string, mixed> $options
     *
     * @throws RuntimeException if the route generator cannot be created
     */
    public function create(array $options = []) : RouteGeneratorInterface;
}
