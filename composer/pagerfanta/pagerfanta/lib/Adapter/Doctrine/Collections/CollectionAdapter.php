<?php

declare (strict_types=1);
namespace OCA\Libresign\3rdparty\Pagerfanta\Doctrine\Collections;

use OCA\Libresign\3rdparty\Doctrine\Common\Collections\ReadableCollection;
use OCA\Libresign\3rdparty\Pagerfanta\Adapter\AdapterInterface;
/**
 * Adapter which calculates pagination from a Doctrine Collection.
 *
 * @template TKey of array-key
 * @template T
 *
 * @implements AdapterInterface<T>
 * @internal
 */
class CollectionAdapter implements AdapterInterface
{
    /**
     * @param ReadableCollection<TKey, T> $collection
     */
    public function __construct(private readonly ReadableCollection $collection)
    {
    }
    /**
     * @return int<0, max>
     */
    public function getNbResults() : int
    {
        return $this->collection->count();
    }
    /**
     * @param int<0, max> $offset
     * @param int<0, max> $length
     *
     * @return iterable<TKey, T>
     */
    public function getSlice(int $offset, int $length) : iterable
    {
        return $this->collection->slice($offset, $length);
    }
}
