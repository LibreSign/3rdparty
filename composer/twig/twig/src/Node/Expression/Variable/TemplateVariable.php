<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OCA\Libresign\Vendor\Twig\Node\Expression\Variable;

use OCA\Libresign\Vendor\Twig\Compiler;
use OCA\Libresign\Vendor\Twig\Node\Expression\TempNameExpression;
/** @internal */
class TemplateVariable extends TempNameExpression
{
    public function getName(Compiler $compiler) : string
    {
        if (null === $this->getAttribute('name')) {
            $this->setAttribute('name', $compiler->getVarName());
        }
        return $this->getAttribute('name');
    }
    public function compile(Compiler $compiler) : void
    {
        $name = $this->getName($compiler);
        if ('_self' === $name) {
            $compiler->raw('$this');
        } else {
            $compiler->raw('$macros[')->string($name)->raw(']');
        }
    }
}
