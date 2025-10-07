<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OCA\Libresign\Vendor\Twig\Node\Expression\Filter;

use OCA\Libresign\Vendor\Twig\Attribute\FirstClassTwigCallableReady;
use OCA\Libresign\Vendor\Twig\Compiler;
use OCA\Libresign\Vendor\Twig\Extension\CoreExtension;
use OCA\Libresign\Vendor\Twig\Node\EmptyNode;
use OCA\Libresign\Vendor\Twig\Node\Expression\AbstractExpression;
use OCA\Libresign\Vendor\Twig\Node\Expression\ConstantExpression;
use OCA\Libresign\Vendor\Twig\Node\Expression\FilterExpression;
use OCA\Libresign\Vendor\Twig\Node\Expression\GetAttrExpression;
use OCA\Libresign\Vendor\Twig\Node\Expression\Ternary\ConditionalTernary;
use OCA\Libresign\Vendor\Twig\Node\Expression\Test\DefinedTest;
use OCA\Libresign\Vendor\Twig\Node\Expression\Variable\ContextVariable;
use OCA\Libresign\Vendor\Twig\Node\Node;
use OCA\Libresign\Vendor\Twig\TwigFilter;
use OCA\Libresign\Vendor\Twig\TwigTest;
/**
 * Returns the value or the default value when it is undefined or empty.
 *
 *  {{ var.foo|default('foo item on var is not defined') }}
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @internal
 */
class DefaultFilter extends FilterExpression
{
    /**
     * @param AbstractExpression $node
     */
    #[FirstClassTwigCallableReady]
    public function __construct(Node $node, TwigFilter|ConstantExpression $filter, Node $arguments, int $lineno)
    {
        if (!$node instanceof AbstractExpression) {
            trigger_deprecation('twig/twig', '3.15', 'Not passing a "%s" instance to the "node" argument of "%s" is deprecated ("%s" given).', AbstractExpression::class, static::class, $node::class);
        }
        if ($filter instanceof TwigFilter) {
            $name = $filter->getName();
            $default = new FilterExpression($node, $filter, $arguments, $node->getTemplateLine());
        } else {
            $name = $filter->getAttribute('value');
            $default = new FilterExpression($node, new TwigFilter('default', [CoreExtension::class, 'default']), $arguments, $node->getTemplateLine());
        }
        if ('default' === $name && ($node instanceof ContextVariable || $node instanceof GetAttrExpression)) {
            $test = new DefinedTest(clone $node, new TwigTest('defined'), new EmptyNode(), $node->getTemplateLine());
            $false = \count($arguments) ? $arguments->getNode('0') : new ConstantExpression('', $node->getTemplateLine());
            $node = new ConditionalTernary($test, $default, $false, $node->getTemplateLine());
        } else {
            $node = $default;
        }
        parent::__construct($node, $filter, $arguments, $lineno);
    }
    public function compile(Compiler $compiler) : void
    {
        $compiler->subcompile($this->getNode('node'));
    }
}
