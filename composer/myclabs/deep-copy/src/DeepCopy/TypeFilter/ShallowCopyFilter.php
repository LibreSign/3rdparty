<?php

namespace OCA\Libresign\Vendor\DeepCopy\TypeFilter;

/**
 * @final
 * @internal
 */
class ShallowCopyFilter implements TypeFilter
{
    /**
     * {@inheritdoc}
     */
    public function apply($element)
    {
        return clone $element;
    }
}
