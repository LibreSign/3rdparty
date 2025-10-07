<?php

namespace OCA\Libresign\Vendor\DeepCopy\Matcher\Doctrine;

use OCA\Libresign\Vendor\DeepCopy\Matcher\Matcher;
use OCA\Libresign\Vendor\Doctrine\Persistence\Proxy;
/**
 * @final
 * @internal
 */
class DoctrineProxyMatcher implements Matcher
{
    /**
     * Matches a Doctrine Proxy class.
     *
     * {@inheritdoc}
     */
    public function matches($object, $property)
    {
        return $object instanceof Proxy;
    }
}
