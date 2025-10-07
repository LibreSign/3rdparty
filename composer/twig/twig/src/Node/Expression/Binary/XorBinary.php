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
namespace OCA\Libresign\3rdparty\Twig\Node\Expression\Binary;

use OCA\Libresign\3rdparty\Twig\Compiler;
use OCA\Libresign\3rdparty\Twig\Node\Expression\ReturnBoolInterface;
/** @internal */
class XorBinary extends AbstractBinary implements ReturnBoolInterface
{
    public function operator(Compiler $compiler) : Compiler
    {
        return $compiler->raw('xor');
    }
}
