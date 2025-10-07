<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 * (c) Armin Ronacher
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OCA\Libresign\Vendor\Twig\Node\Expression\Binary;

use OCA\Libresign\Vendor\Twig\Compiler;
use OCA\Libresign\Vendor\Twig\Node\Expression\ReturnStringInterface;
/** @internal */
class ConcatBinary extends AbstractBinary implements ReturnStringInterface
{
    public function operator(Compiler $compiler) : Compiler
    {
        return $compiler->raw('.');
    }
}
