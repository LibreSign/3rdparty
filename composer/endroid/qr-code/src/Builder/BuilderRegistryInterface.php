<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\Endroid\QrCode\Builder;

/** @internal */
interface BuilderRegistryInterface
{
    public function getBuilder(string $name) : BuilderInterface;
    public function addBuilder(string $name, BuilderInterface $builder) : void;
}
