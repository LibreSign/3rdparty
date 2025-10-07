<?php

declare (strict_types=1);
namespace OCA\Libresign\3rdparty\Pagerfanta\Doctrine\MongoDBODM;

use OCA\Libresign\3rdparty\Doctrine\ODM\MongoDB\Aggregation\Builder;
use OCA\Libresign\3rdparty\Pagerfanta\Adapter\AdapterInterface;
/**
 * Adapter which calculates pagination from a Doctrine MongoDB ODM Aggregation Builder.
 *
 * @template T
 *
 * @implements AdapterInterface<T>
 * @internal
 */
class AggregationAdapter implements AdapterInterface
{
    public function __construct(private readonly Builder $aggregationBuilder)
    {
    }
    /**
     * @return int<0, max>
     */
    public function getNbResults() : int
    {
        $aggregationBuilder = clone $this->aggregationBuilder;
        return $aggregationBuilder->hydrate(null)->count('numResults')->getAggregation()->getIterator()->toArray()[0]['numResults'] ?? 0;
    }
    /**
     * @param int<0, max> $offset
     * @param int<0, max> $length
     *
     * @return iterable<array-key, T>
     */
    public function getSlice(int $offset, int $length) : iterable
    {
        $aggregationBuilder = clone $this->aggregationBuilder;
        return $aggregationBuilder->skip($offset)->limit($length)->getAggregation()->getIterator();
    }
}
