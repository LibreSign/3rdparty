<?php

namespace OCA\Libresign\Vendor\Twig\Node;

use OCA\Libresign\Vendor\Twig\Attribute\YieldReady;
use OCA\Libresign\Vendor\Twig\Compiler;
/**
 * Represents a types node.
 *
 * @author Jeroen Versteeg <jeroen@alisqi.com>
 * @internal
 */
#[YieldReady]
class TypesNode extends Node
{
    /**
     * @param array<string, array{type: string, optional: bool}> $types
     */
    public function __construct(array $types, int $lineno)
    {
        parent::__construct([], ['mapping' => $types], $lineno);
    }
    /**
     * @return void
     */
    public function compile(Compiler $compiler)
    {
        // Don't compile anything.
    }
}
