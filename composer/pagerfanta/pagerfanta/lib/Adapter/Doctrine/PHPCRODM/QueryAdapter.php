<?php

declare (strict_types=1);
namespace OCA\Libresign\3rdparty\Pagerfanta\Doctrine\PHPCRODM;

use OCA\Libresign\3rdparty\Doctrine\ODM\PHPCR\Query\Builder\QueryBuilder;
use OCA\Libresign\3rdparty\Doctrine\ODM\PHPCR\Query\Query;
use OCA\Libresign\3rdparty\Pagerfanta\Adapter\AdapterInterface;
/**
 * Adapter which calculates pagination from a Doctrine PHPCR ODM QueryBuilder.
 *
 * @template T
 *
 * @implements AdapterInterface<T>
 * @internal
 */
class QueryAdapter implements AdapterInterface
{
    public function __construct(private readonly QueryBuilder $queryBuilder)
    {
    }
    /**
     * @return int<0, max>
     */
    public function getNbResults() : int
    {
        return $this->queryBuilder->getQuery()->execute(null, Query::HYDRATE_PHPCR)->getRows()->count();
    }
    /**
     * @param int<0, max> $offset
     * @param int<0, max> $length
     *
     * @return iterable<array-key, T>
     */
    public function getSlice(int $offset, int $length) : iterable
    {
        return $this->queryBuilder->getQuery()->setMaxResults($length)->setFirstResult($offset)->execute();
    }
}
