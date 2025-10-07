<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\Pagerfanta\Doctrine\ORM;

use OCA\Libresign\Vendor\Doctrine\ORM\Query;
use OCA\Libresign\Vendor\Doctrine\ORM\QueryBuilder;
use OCA\Libresign\Vendor\Doctrine\ORM\Tools\Pagination\Paginator;
use OCA\Libresign\Vendor\Pagerfanta\Adapter\AdapterInterface;
/**
 * Adapter which calculates pagination from a Doctrine ORM Query or QueryBuilder.
 *
 * @template T
 *
 * @implements AdapterInterface<T>
 * @internal
 */
class QueryAdapter implements AdapterInterface
{
    /**
     * @var Paginator<T>
     */
    private readonly Paginator $paginator;
    /**
     * @param bool      $fetchJoinCollection Whether the query joins a collection (true by default)
     * @param bool|null $useOutputWalkers    Flag indicating whether output walkers are used in the paginator
     */
    public function __construct(Query|QueryBuilder $query, bool $fetchJoinCollection = \true, ?bool $useOutputWalkers = null)
    {
        $this->paginator = new Paginator($query, $fetchJoinCollection);
        $this->paginator->setUseOutputWalkers($useOutputWalkers);
    }
    /**
     * @return int<0, max>
     */
    public function getNbResults() : int
    {
        return \count($this->paginator);
    }
    /**
     * @param int<0, max> $offset
     * @param int<0, max> $length
     *
     * @return \Traversable<array-key, T>
     */
    public function getSlice(int $offset, int $length) : iterable
    {
        $this->paginator->getQuery()->setFirstResult($offset)->setMaxResults($length);
        return $this->paginator->getIterator();
    }
}
