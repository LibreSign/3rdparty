<?php

declare (strict_types=1);
namespace OCA\Libresign\3rdparty\Pagerfanta\Doctrine\MongoDBODM;

use OCA\Libresign\3rdparty\Doctrine\ODM\MongoDB\Query\Builder;
use OCA\Libresign\3rdparty\Pagerfanta\Adapter\AdapterInterface;
/**
 * Adapter which calculates pagination from a Doctrine MongoDB ODM QueryBuilder.
 *
 * @template T
 *
 * @implements AdapterInterface<T>
 * @internal
 */
class QueryAdapter implements AdapterInterface
{
    public function __construct(private readonly Builder $queryBuilder)
    {
    }
    /**
     * @return int<0, max>
     */
    public function getNbResults() : int
    {
        $qb = clone $this->queryBuilder;
        return $qb->limit(0)->skip(0)->count()->getQuery()->execute();
    }
    /**
     * @param int<0, max> $offset
     * @param int<0, max> $length
     *
     * @return iterable<array-key, T>
     */
    public function getSlice(int $offset, int $length) : iterable
    {
        return $this->queryBuilder->limit($length)->skip($offset)->getQuery()->execute();
    }
}
