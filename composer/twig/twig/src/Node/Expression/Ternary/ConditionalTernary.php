<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OCA\Libresign\Vendor\Twig\Node\Expression\Ternary;

use OCA\Libresign\Vendor\Twig\Compiler;
use OCA\Libresign\Vendor\Twig\Node\Expression\AbstractExpression;
use OCA\Libresign\Vendor\Twig\Node\Expression\OperatorEscapeInterface;
use OCA\Libresign\Vendor\Twig\Node\Expression\ReturnPrimitiveTypeInterface;
use OCA\Libresign\Vendor\Twig\Node\Expression\Test\TrueTest;
use OCA\Libresign\Vendor\Twig\TwigTest;
/** @internal */
final class ConditionalTernary extends AbstractExpression implements OperatorEscapeInterface
{
    public function __construct(AbstractExpression $test, AbstractExpression $left, AbstractExpression $right, int $lineno)
    {
        if (!$test instanceof ReturnPrimitiveTypeInterface) {
            $test = new TrueTest($test, new TwigTest('true'), null, $test->getTemplateLine());
        }
        parent::__construct(['test' => $test, 'left' => $left, 'right' => $right], [], $lineno);
    }
    public function compile(Compiler $compiler) : void
    {
        $compiler->raw('((')->subcompile($this->getNode('test'))->raw(') ? (')->subcompile($this->getNode('left'))->raw(') : (')->subcompile($this->getNode('right'))->raw('))');
    }
    public function getOperandNamesToEscape() : array
    {
        return ['left', 'right'];
    }
}
