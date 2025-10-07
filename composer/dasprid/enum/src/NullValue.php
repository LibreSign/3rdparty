<?php

declare (strict_types=1);
namespace OCA\Libresign\3rdparty\DASPRiD\Enum;

use OCA\Libresign\3rdparty\DASPRiD\Enum\Exception\CloneNotSupportedException;
use OCA\Libresign\3rdparty\DASPRiD\Enum\Exception\SerializeNotSupportedException;
use OCA\Libresign\3rdparty\DASPRiD\Enum\Exception\UnserializeNotSupportedException;
/** @internal */
final class NullValue
{
    /**
     * @var self
     */
    private static $instance;
    private function __construct()
    {
    }
    public static function instance() : self
    {
        return self::$instance ?: (self::$instance = new self());
    }
    /**
     * Forbid cloning enums.
     *
     * @throws CloneNotSupportedException
     */
    public final function __clone()
    {
        throw new CloneNotSupportedException();
    }
    /**
     * Forbid serializing enums.
     *
     * @throws SerializeNotSupportedException
     */
    public final function __sleep() : array
    {
        throw new SerializeNotSupportedException();
    }
    /**
     * Forbid serializing enums.
     *
     * @throws SerializeNotSupportedException
     */
    public final function __serialize() : array
    {
        throw new SerializeNotSupportedException();
    }
    /**
     * Forbid unserializing enums.
     *
     * @throws UnserializeNotSupportedException
     */
    public final function __wakeup() : void
    {
        throw new UnserializeNotSupportedException();
    }
    /**
     * Forbid unserializing enums.
     *
     * @throws UnserializeNotSupportedException
     */
    public final function __unserialize($arg) : void
    {
        throw new UnserializeNotSupportedException();
    }
}
