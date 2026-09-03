<?php

/**
 * ReadBytes trait
 *
 * PHP version 8.1+
 *
 * @author    Jim Wigginton <terrafrost@php.net>
 * @copyright 2022-2026 Jim Wigginton
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link      https://phpseclib.com/
 */
declare (strict_types=1);
namespace OCA\Libresign\Vendor\phpseclib4\System\SSH\Common\Traits;

use OCA\Libresign\Vendor\phpseclib4\Exception\ConnectionClosedException;
use OCA\Libresign\Vendor\phpseclib4\Exception\UnexpectedValueException;
/**
 * ReadBytes trait
 *
 * @author  Jim Wigginton <terrafrost@php.net>
 * @internal
 */
trait ReadBytes
{
    /**
     * Read data
     */
    public function readBytes(int $length) : string
    {
        $temp = \fread($this->fsock, $length);
        if ($temp === \false) {
            throw new ConnectionClosedException('\\fread() failed.');
        }
        if (\strlen($temp) !== $length) {
            throw new UnexpectedValueException("Expected {$length} bytes; got " . \strlen($temp));
        }
        return $temp;
    }
}
