<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OCA\Libresign\Vendor\Twig\Node\Expression\Test;

use OCA\Libresign\Vendor\Twig\Node\Expression\TestExpression;
/**
 * @internal
 */
final class NonStringCoercingInputTest extends TestExpression
{
    public function getStringCoercedChildNames() : array
    {
        return $this->hasNode('arguments') ? ['arguments'] : [];
    }
}
