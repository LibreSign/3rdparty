<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\MaxMind\Exception;

/**
 * Thrown when the IP address is not found in the database.
 * @internal
 */
class IpAddressNotFoundException extends InvalidRequestException
{
}
