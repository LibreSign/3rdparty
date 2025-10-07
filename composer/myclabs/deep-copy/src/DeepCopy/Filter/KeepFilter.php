<?php

namespace OCA\Libresign\Vendor\DeepCopy\Filter;

/** @internal */
class KeepFilter implements Filter
{
    /**
     * Keeps the value of the object property.
     *
     * {@inheritdoc}
     */
    public function apply($object, $property, $objectCopier)
    {
        // Nothing to do
    }
}
