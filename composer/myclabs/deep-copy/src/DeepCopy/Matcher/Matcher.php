<?php

namespace OCA\Libresign\3rdparty\DeepCopy\Matcher;

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
