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
/**
 * Represents an empty node.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @internal
 */
#[YieldReady]
final class EmptyNode extends Node
{
    public function __construct(int $lineno = 0)
    {
        parent::__construct([], [], $lineno);
    }
    public function setNode(string $name, Node $node) : void
    {
        throw new \LogicException('EmptyNode cannot have children.');
    }
}
