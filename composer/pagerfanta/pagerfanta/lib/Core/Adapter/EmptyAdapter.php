<?php

declare (strict_types=1);
namespace OCA\Libresign\3rdparty\Pagerfanta\Adapter;

/**
 * An adapter that is always empty.
 *
 * @template-implements AdapterInterface<never>
 * @internal
 */
class EmptyAdapter implements AdapterInterface
{
    public function getNbResults() : int
    {
        return 0;
    }
    public function getSlice(int $offset, int $length) : iterable
    {
        return [];
    }
}
