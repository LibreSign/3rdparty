<?php

/**
 * Signable interface
 *
 * @author    Jim Wigginton <terrafrost@php.net>
 * @copyright 2025-2026 Jim Wigginton
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link      https://phpseclib.com/
 */
declare (strict_types=1);
namespace OCA\Libresign\Vendor\phpseclib4\File\Common;

use OCA\Libresign\Vendor\phpseclib4\Crypt\Common\PrivateKey;
use OCA\Libresign\Vendor\phpseclib4\File\X509;
/**
 * Signable interface
 *
 * @author  Jim Wigginton <terrafrost@php.net>
 * @internal
 */
interface Signable
{
    public function getSignableSection() : string;
    public function setSignature(string $signature) : void;
    public function identifySignatureAlgorithm(PrivateKey $key) : void;
    public function copySigningX509Attributes(X509 $x509) : void;
}
