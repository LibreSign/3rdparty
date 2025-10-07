<?php

declare (strict_types=1);
namespace OCA\Libresign\3rdparty\Pagerfanta\Doctrine\Collections;

use OCA\Libresign\3rdparty\Doctrine\Common\Collections\Criteria;
use OCA\Libresign\3rdparty\Doctrine\Common\Collections\Selectable;
use OCA\Libresign\3rdparty\Pagerfanta\Adapter\AdapterInterface;
/**
 * Adapter which calculates pagination from a Selectable instance.
 *
 * @template TKey of array-key
 * @template T
 *
 * @implements AdapterInterface<T>
 * @internal
 */
class SelectableAdapter implements AdapterInterface
{
    /**
     * @param Selectable<TKey, T> $selectable
     */
    public function __construct(private readonly Selectable $selectable, private readonly Criteria $criteria)
    {
    }
    /**
     * @return int<0, max>
     */
    public function getNbResults() : int
    {
        return $this->selectable->matching($this->createCriteria(0, null))->count();
    }
    /**
     * @param int<0, max> $offset
     * @param int<0, max> $length
     *
     * @return iterable<array-key, T>
     */
    public function getSlice(int $offset, int $length) : iterable
    {
        return $this->selectable->matching($this->createCriteria($offset, $length));
    }
    /**
     * @param int<0, max>      $firstResult
     * @param int<0, max>|null $maxResult
     */
    private function createCriteria(int $firstResult, ?int $maxResult) : Criteria
    {
        $criteria = clone $this->criteria;
        $criteria->setFirstResult($firstResult);
        $criteria->setMaxResults($maxResult);
        return $criteria;
    }
}
