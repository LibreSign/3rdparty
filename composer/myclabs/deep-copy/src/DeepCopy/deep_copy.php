<?php

namespace OCA\Libresign\Vendor\DeepCopy;

use function function_exists;
if (\false === function_exists('OCA\\Libresign\\Vendor\\DeepCopy\\deep_copy')) {
    /**
     * Deep copies the given value.
     *
     * @param mixed $value
     * @param bool  $useCloneMethod
     *
     * @return mixed
     * @internal
     */
    function deep_copy($value, $useCloneMethod = \false)
    {
        return (new DeepCopy($useCloneMethod))->copy($value);
    }
}
