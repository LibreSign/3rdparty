<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OCA\Libresign\3rdparty\Twig\Node\Expression;

use OCA\Libresign\3rdparty\Twig\Compiler;
use OCA\Libresign\3rdparty\Twig\Node\Expression\Variable\ContextVariable;
/** @internal */
class ListExpression extends AbstractExpression
{
    /**
     * @param array<ContextVariable> $items
     */
    public function __construct(array $items, int $lineno)
    {
        parent::__construct($items, [], $lineno);
    }
    public function compile(Compiler $compiler) : void
    {
        foreach ($this as $i => $name) {
            if ($i) {
                $compiler->raw(', ');
            }
            $compiler->raw('$__')->raw($name->getAttribute('name'))->raw('__');
        }
    }
}
