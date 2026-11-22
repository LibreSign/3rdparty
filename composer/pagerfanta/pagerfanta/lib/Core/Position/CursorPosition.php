<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\Pagerfanta\Position;

use OCA\Libresign\Vendor\Pagerfanta\Cursor\Cursor;
/**
 * A position within a cursor paginated list, identified by the cursor pointing to it.
 * @internal
 */
final class CursorPosition implements Position
{
    public function __construct(public readonly Cursor $cursor)
    {
    }
}
