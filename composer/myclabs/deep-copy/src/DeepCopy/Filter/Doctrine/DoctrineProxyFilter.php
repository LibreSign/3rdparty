<?php

namespace OCA\Libresign\Vendor\DeepCopy\Filter\Doctrine;

use OCA\Libresign\Vendor\DeepCopy\Filter\Filter;
/**
 * @final
 * @internal
 */
class DoctrineProxyFilter implements Filter
{
    /**
     * Triggers the magic method __load() on a Doctrine Proxy class to load the
     * actual entity from the database.
     *
     * {@inheritdoc}
     */
    public function apply($object, $property, $objectCopier)
    {
        $object->__load();
    }
}
