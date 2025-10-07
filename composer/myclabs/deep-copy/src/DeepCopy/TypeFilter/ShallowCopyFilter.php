<?php

namespace OCA\Libresign\3rdparty\DeepCopy\TypeFilter;

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
