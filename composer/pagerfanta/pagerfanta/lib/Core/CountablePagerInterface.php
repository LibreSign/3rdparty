<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\Pagerfanta;

use OCA\Libresign\Vendor\Pagerfanta\Position\Position;
/**
 * A pager which knows the total number of results in the list.
 *
 * @template-covariant T
 * @template-covariant TPosition of Position
 *
 * @extends PagerInterface<T, TPosition>
 * @internal
 */
interface CountablePagerInterface extends PagerInterface
{
    /**
     * @return int<0, max>
     */
    public function getNbResults() : int;
}
