<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\MaxMind\Exception;

/**
 * Thrown when the account is out of credits.
 * @internal
 */
class InsufficientFundsException extends InvalidRequestException
{
}
