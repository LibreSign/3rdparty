<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\Pagerfanta\Adapter;

use OCA\Libresign\Vendor\Pagerfanta\Exception\NotValidResultCountException;
/**
 * An adapter which can report the total number of results in the list.
 * @internal
 */
interface CountableAdapterInterface
{
    /**
     * Returns the number of results for the list.
     *
     * @return int<0, max>
     *
     * @throws NotValidResultCountException if the number of results is less than zero
     */
    public function getNbResults() : int;
}
