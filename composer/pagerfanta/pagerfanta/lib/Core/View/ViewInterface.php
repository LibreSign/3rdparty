<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\Pagerfanta\View;

use OCA\Libresign\Vendor\Pagerfanta\PagerfantaInterface;
use OCA\Libresign\Vendor\Pagerfanta\PagerInterface;
use OCA\Libresign\Vendor\Pagerfanta\Position\Position;
use OCA\Libresign\Vendor\Pagerfanta\RouteGenerator\PositionRouteGeneratorInterface;
use OCA\Libresign\Vendor\Pagerfanta\RouteGenerator\RouteGeneratorInterface;
/**
 * In 5.0, the render() method will accept any {@see PagerInterface} and a {@see PositionRouteGeneratorInterface}, and the
 * supports() method will be added. Implement {@see PagerViewInterface} to prepare for this change.
 *
 * @method bool supports(PagerfantaInterface<mixed>|PagerInterface<mixed, Position> $pager)
 * @internal
 */
interface ViewInterface
{
    /**
     * Passing a page number based route generator is deprecated since 4.10, views should accept a {@see PositionRouteGeneratorInterface}.
     *
     * @param PagerfantaInterface<mixed>                                                    $pagerfanta
     * @param PositionRouteGeneratorInterface|RouteGeneratorInterface|callable(int): string $routeGenerator
     * @param array<string, mixed>                                                          $options
     */
    public function render(PagerfantaInterface $pagerfanta, callable $routeGenerator, array $options = []) : string;
    public function getName() : string;
}
