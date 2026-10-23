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
use OCA\Libresign\Vendor\Twig\Node\Expression\ReturnBoolInterface;
use OCA\Libresign\Vendor\Twig\Node\Expression\Test\TrueTest;
use OCA\Libresign\Vendor\Twig\Node\Node;
/** @internal */
class OrBinary extends AbstractBinary implements ReturnBoolInterface
{
    public function __construct(Node $left, Node $right, int $lineno)
    {
        parent::__construct(TrueTest::wrap($left), TrueTest::wrap($right), $lineno);
    }
    public function operator(Compiler $compiler) : Compiler
    {
        return $compiler->raw('||');
    }
}
