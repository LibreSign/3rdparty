<?php

namespace OCA\Libresign\Vendor\DeepCopy\TypeFilter;

/** @internal */
interface TypeFilter
{
    /**
     * Applies the filter to the object.
     *
     * @param mixed $element
     */
    public function apply($element);
}
