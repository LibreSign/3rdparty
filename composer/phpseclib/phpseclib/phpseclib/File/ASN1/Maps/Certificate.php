<?php

/**
 * Certificate
 *
 * PHP version 5
 *
 * @author    Jim Wigginton <terrafrost@php.net>
 * @copyright 2016 Jim Wigginton
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link      http://phpseclib.sourceforge.net
 */
namespace OCA\Libresign\3rdparty\phpseclib3\File\ASN1\Maps;

use OCA\Libresign\3rdparty\phpseclib3\File\ASN1;
/**
 * Certificate
 *
 * @author  Jim Wigginton <terrafrost@php.net>
 * @internal
 */
abstract class Certificate
{
    const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['tbsCertificate' => TBSCertificate::MAP, 'signatureAlgorithm' => AlgorithmIdentifier::MAP, 'signature' => ['type' => ASN1::TYPE_BIT_STRING]]];
}
