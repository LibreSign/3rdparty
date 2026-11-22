<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\Pagerfanta\Adapter;

/**
 * An adapter supporting offset based pagination which can report the total number of results.
 *
 * @template-covariant T
 *
 * @extends OffsetAdapterInterface<T>
 * @internal
 */
interface AdapterInterface extends OffsetAdapterInterface, CountableAdapterInterface
{
}
