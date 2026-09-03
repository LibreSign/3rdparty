<?php

/**
 * GMP Modular Exponentiation Engine
 *
 * PHP version 8.1+
 *
 * @author    Jim Wigginton <terrafrost@php.net>
 * @copyright 2017-2026 Jim Wigginton
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link      https://phpseclib.com/
 */
declare (strict_types=1);
namespace OCA\Libresign\Vendor\phpseclib4\Math\BigInteger\Engines\GMP;

use OCA\Libresign\Vendor\phpseclib4\Math\BigInteger\Engines\GMP;
/**
 * GMP Modular Exponentiation Engine
 *
 * @author  Jim Wigginton <terrafrost@php.net>
 * @psalm-api
 * @internal
 */
abstract class DefaultEngine extends GMP
{
    /**
     * Performs modular exponentiation.
     */
    protected static function powModHelper(GMP $x, GMP $e, GMP $n) : GMP
    {
        $temp = new GMP();
        $temp->value = \gmp_powm($x->value, $e->value, $n->value);
        return $x->normalize($temp);
    }
}
