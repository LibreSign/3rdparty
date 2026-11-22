<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\Pagerfanta\Doctrine\ORM;

use OCA\Libresign\Vendor\Doctrine\ORM\Query;
use OCA\Libresign\Vendor\Doctrine\ORM\QueryBuilder;
use OCA\Libresign\Vendor\Doctrine\ORM\Tools\Pagination\OffsetPaginator;
use OCA\Libresign\Vendor\Doctrine\ORM\Tools\Pagination\Paginator;
use OCA\Libresign\Vendor\Doctrine\ORM\Tools\Pagination\Window;
use OCA\Libresign\Vendor\Doctrine\ORM\Tools\Pagination\WindowPage;
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
     * @var OffsetPaginator<T>|Paginator<T>
     */
    private readonly OffsetPaginator|Paginator $paginator;
    /**
     * @var WindowPage<T>|null
     */
    private ?WindowPage $page = null;
    /**
     * @param bool      $fetchJoinCollection Whether the query joins a collection (true by default)
     * @param bool|null $useOutputWalkers    Flag indicating whether output walkers are used in the paginator
     */
    public function __construct(private readonly Query|QueryBuilder $query, bool $fetchJoinCollection = \true, ?bool $useOutputWalkers = null)
    {
        if (\class_exists(OffsetPaginator::class)) {
            $this->paginator = new OffsetPaginator($fetchJoinCollection, $useOutputWalkers);
        } else {
            $this->paginator = new Paginator($query, $fetchJoinCollection);
            $this->paginator->setUseOutputWalkers($useOutputWalkers);
        }
    }
    /**
     * @return int<0, max>
     */
    public function getNbResults() : int
    {
        if ($this->paginator instanceof OffsetPaginator) {
            /*
             * A slice may not have been fetched yet, so there isn't always a window to reuse.
             * In those cases, we probe with a minimum-sized window to get the total count,
             * with the page later replaced by getSlice().
             */
            $this->page ??= $this->paginator->paginate($this->query, new Window(0, 1));
            return $this->page->getTotalCount();
        }
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
        if ($this->paginator instanceof OffsetPaginator) {
            if (0 === $length) {
                return new \EmptyIterator();
            }
            $this->page = $this->paginator->paginate($this->query, new Window($offset, $length));
            return $this->page->getIterator();
        }
        $this->paginator->getQuery()->setFirstResult($offset)->setMaxResults($length);
        return $this->paginator->getIterator();
    }
}
