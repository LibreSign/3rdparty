<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OCA\Libresign\Vendor\Random;

use OCA\Libresign\Vendor\Symfony\Polyfill\Php82\NoDynamicProperties;
if (\PHP_VERSION_ID < 80200) {
    /** @internal */
    class RandomException extends \Exception
    {
        use NoDynamicProperties;
    }
}
