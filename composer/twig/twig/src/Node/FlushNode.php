<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OCA\Libresign\3rdparty\Twig\Node;

use OCA\Libresign\3rdparty\Twig\Attribute\YieldReady;
use OCA\Libresign\3rdparty\Twig\Compiler;
/**
 * Represents a flush node.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @internal
 */
#[YieldReady]
class FlushNode extends Node
{
    public function __construct(int $lineno)
    {
        parent::__construct([], [], $lineno);
    }
    public function compile(Compiler $compiler) : void
    {
        $compiler->addDebugInfo($this);
        if ($compiler->getEnvironment()->useYield()) {
            $compiler->write("yield '';\n");
        }
        $compiler->write("flush();\n");
    }
}
