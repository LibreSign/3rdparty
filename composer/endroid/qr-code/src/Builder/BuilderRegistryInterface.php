<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\Endroid\QrCode\Builder;

/** @internal */
interface BuilderRegistryInterface
{
    public function set(string $name, BuilderInterface $builder) : void;
    public function get(string $name) : BuilderInterface;
}
