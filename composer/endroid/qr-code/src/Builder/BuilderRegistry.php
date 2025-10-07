<?php

declare (strict_types=1);
namespace OCA\Libresign\3rdparty\Endroid\QrCode\Builder;

/** @internal */
final class BuilderRegistry implements BuilderRegistryInterface
{
    /** @var array<BuilderInterface> */
    private array $builders = [];
    public function getBuilder(string $name) : BuilderInterface
    {
        if (!isset($this->builders[$name])) {
            throw new \Exception(\sprintf('Builder with name "%s" not available from registry', $name));
        }
        return $this->builders[$name];
    }
    public function addBuilder(string $name, BuilderInterface $builder) : void
    {
        $this->builders[$name] = $builder;
    }
}
