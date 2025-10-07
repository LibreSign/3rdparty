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
 * @author Fabien Potencier <fabien@symfony.com>
 * @internal
 */
#[YieldReady]
class CheckSecurityCallNode extends Node
{
    /**
     * @return void
     */
    public function compile(Compiler $compiler)
    {
        $compiler->write("\$this->sandbox = \$this->extensions[SandboxExtension::class];\n")->write("\$this->checkSecurity();\n");
    }
}
