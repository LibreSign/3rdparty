<?php

declare (strict_types=1);
namespace OCA\Libresign\3rdparty\Pagerfanta\View;

use OCA\Libresign\3rdparty\Pagerfanta\PagerfantaInterface;
use OCA\Libresign\3rdparty\Pagerfanta\RouteGenerator\RouteGeneratorInterface;
/** @internal */
interface ViewInterface
{
    /**
     * @param PagerfantaInterface<mixed>       $pagerfanta
     * @param callable|RouteGeneratorInterface $routeGenerator
     * @param array<string, mixed>             $options
     *
     * @phpstan-param callable(int $page): string|RouteGeneratorInterface $routeGenerator
     */
    public function render(PagerfantaInterface $pagerfanta, callable $routeGenerator, array $options = []) : string;
    public function getName() : string;
}
