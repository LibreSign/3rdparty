<?php

namespace OCA\Libresign\Vendor\DeepCopy\Filter;

/**
 * Filter to apply to a property while copying an object
 * @internal
 */
interface Filter
{
    /**
     * Applies the filter to the object.
     *
     * @param object   $object
     * @param string   $property
     * @param callable $objectCopier
     */
    public function apply($object, $property, $objectCopier);
}
