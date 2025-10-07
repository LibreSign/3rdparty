<?php

namespace OCA\Libresign\Vendor\DeepCopy\Matcher;

/** @internal */
interface Matcher
{
    /**
     * @param object $object
     * @param string $property
     *
     * @return boolean
     */
    public function matches($object, $property);
}
