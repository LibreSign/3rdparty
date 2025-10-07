<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\Pagerfanta\Adapter;

use OCA\Libresign\Vendor\Pagerfanta\Exception\NotValidResultCountException;
/**
 * @template-covariant T
 * @internal
 */
interface AdapterInterface
{
    /**
     * Returns the number of results for the list.
     *
     * @return int<0, max>
     *
     * @throws NotValidResultCountException if the number of results is less than zero
     */
    public function getNbResults() : int;
    /**
     * Returns a slice of the results representing the current page of items in the list.
     *
     * @param int<0, max> $offset
     * @param int<0, max> $length
     *
     * @return iterable<array-key, T>
     */
    public function getSlice(int $offset, int $length) : iterable;
}
