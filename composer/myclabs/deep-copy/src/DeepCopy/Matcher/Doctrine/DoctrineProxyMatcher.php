<?php

namespace OCA\Libresign\3rdparty\DeepCopy\Matcher\Doctrine;

use OCA\Libresign\3rdparty\DeepCopy\Matcher\Matcher;
use OCA\Libresign\3rdparty\Doctrine\Persistence\Proxy;
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
