<?php

namespace OCA\Libresign\Vendor;

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use OCA\Libresign\Vendor\Symfony\Polyfill\Intl\Normalizer as p;
if (!\function_exists('normalizer_is_normalized')) {
    /** @internal */
    function normalizer_is_normalized(?string $string, ?int $form = p\Normalizer::FORM_C) : bool
    {
        return p\Normalizer::isNormalized((string) $string, (int) $form);
    }
}
if (!\function_exists('normalizer_normalize')) {
    /** @internal */
    function normalizer_normalize(?string $string, ?int $form = p\Normalizer::FORM_C) : string|false
    {
        return p\Normalizer::normalize((string) $string, (int) $form);
    }
}
if (!\function_exists('normalizer_get_raw_decomposition')) {
    /** @internal */
    function normalizer_get_raw_decomposition(?string $string, ?int $form = p\Normalizer::FORM_C) : ?string
    {
        return p\Normalizer::getRawDecomposition((string) $string, (int) $form);
    }
}
