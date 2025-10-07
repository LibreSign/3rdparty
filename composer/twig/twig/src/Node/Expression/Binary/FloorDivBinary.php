<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OCA\Libresign\Vendor\Twig\Node\Expression\Binary;

use OCA\Libresign\Vendor\Twig\Compiler;
use OCA\Libresign\Vendor\Twig\Node\Expression\ReturnNumberInterface;
/** @internal */
class FloorDivBinary extends AbstractBinary implements ReturnNumberInterface
{
    public function compile(Compiler $compiler) : void
    {
        $compiler->raw('(int) floor(');
        parent::compile($compiler);
        $compiler->raw(')');
    }
    public function operator(Compiler $compiler) : Compiler
    {
        return $compiler->raw('/');
    }
}
