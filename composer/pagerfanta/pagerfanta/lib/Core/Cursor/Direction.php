<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\Pagerfanta\Cursor;

/**
 * The direction to paginate in relative to a cursor.
 * @internal
 */
enum Direction
{
    case Next;
    case Previous;
}
