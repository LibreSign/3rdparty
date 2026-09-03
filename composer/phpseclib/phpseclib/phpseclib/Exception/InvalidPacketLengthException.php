<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\phpseclib4\Exception;

/**
 * Indicates an absent or malformed packet length header
 * @internal
 */
class InvalidPacketLengthException extends ConnectionClosedException
{
}
