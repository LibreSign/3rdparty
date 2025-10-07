<?php

declare (strict_types=1);
namespace OCA\Libresign\3rdparty\Pagerfanta\View;

use OCA\Libresign\3rdparty\Pagerfanta\Exception\InvalidArgumentException;
/** @internal */
interface ViewFactoryInterface
{
    /**
     * @param array<string, ViewInterface> $views
     */
    public function add(array $views) : void;
    /**
     * @return array<string, ViewInterface>
     */
    public function all() : array;
    public function clear() : void;
    /**
     * @throws InvalidArgumentException if the view does not exist
     */
    public function get(string $name) : ViewInterface;
    public function has(string $name) : bool;
    public function remove(string $name) : void;
    public function set(string $name, ViewInterface $view) : void;
}
