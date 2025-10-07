<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\Pagerfanta\RouteGenerator;

/** @internal */
interface RouteGeneratorInterface
{
    /**
     * Generates the URL for a page item in a paginator.
     *
     * @return string The page URL
     */
    public function __invoke(int $page) : string;
}
