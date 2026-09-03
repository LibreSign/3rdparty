<?php

/**
 * DigestedData
 *
 * PHP version 8.1+
 *
 * @author    Jim Wigginton <terrafrost@php.net>
 * @copyright 2016-2026 Jim Wigginton
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link      https://phpseclib.com/
 */
declare (strict_types=1);
namespace OCA\Libresign\Vendor\phpseclib4\File\ASN1\Maps;

use OCA\Libresign\Vendor\phpseclib4\File\ASN1;
/**
 * DigestedData
 *
 * @author  Jim Wigginton <terrafrost@php.net>
 * @internal
 */
abstract class DigestedData
{
    public const MAP = ['type' => ASN1::TYPE_SEQUENCE, 'children' => ['version' => CMSVersion::MAP, 'digestAlgorithm' => DigestAlgorithmIdentifier::MAP, 'encapContentInfo' => EncapsulatedContentInfo::MAP, 'digest' => Digest::MAP]];
}
