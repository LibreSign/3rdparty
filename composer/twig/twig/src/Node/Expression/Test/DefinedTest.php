<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OCA\Libresign\3rdparty\Twig\Node\Expression\Test;

use OCA\Libresign\3rdparty\Twig\Attribute\FirstClassTwigCallableReady;
use OCA\Libresign\3rdparty\Twig\Compiler;
use OCA\Libresign\3rdparty\Twig\Error\SyntaxError;
use OCA\Libresign\3rdparty\Twig\Node\Expression\AbstractExpression;
use OCA\Libresign\3rdparty\Twig\Node\Expression\ArrayExpression;
use OCA\Libresign\3rdparty\Twig\Node\Expression\BlockReferenceExpression;
use OCA\Libresign\3rdparty\Twig\Node\Expression\ConstantExpression;
use OCA\Libresign\3rdparty\Twig\Node\Expression\FunctionExpression;
use OCA\Libresign\3rdparty\Twig\Node\Expression\GetAttrExpression;
use OCA\Libresign\3rdparty\Twig\Node\Expression\MacroReferenceExpression;
use OCA\Libresign\3rdparty\Twig\Node\Expression\MethodCallExpression;
use OCA\Libresign\3rdparty\Twig\Node\Expression\SupportDefinedTestInterface;
use OCA\Libresign\3rdparty\Twig\Node\Expression\TestExpression;
use OCA\Libresign\3rdparty\Twig\Node\Expression\Variable\ContextVariable;
use OCA\Libresign\3rdparty\Twig\Node\Node;
use OCA\Libresign\3rdparty\Twig\TwigTest;
/**
 * Checks if a variable is defined in the current context.
 *
 *    {# defined works with variable names and variable attributes #}
 *    {% if foo is defined %}
 *        {# ... #}
 *    {% endif %}
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @internal
 */
class DefinedTest extends TestExpression
{
    /**
     * @param AbstractExpression $node
     */
    #[FirstClassTwigCallableReady]
    public function __construct(Node $node, TwigTest|string $name, ?Node $arguments, int $lineno)
    {
        if (!$node instanceof AbstractExpression) {
            trigger_deprecation('twig/twig', '3.15', 'Not passing a "%s" instance to the "node" argument of "%s" is deprecated ("%s" given).', AbstractExpression::class, static::class, $node::class);
        }
        if (!$node instanceof SupportDefinedTestInterface) {
            throw new SyntaxError('The "defined" test only works with simple variables.', $lineno);
        }
        $node->enableDefinedTest();
        if (\is_string($name) && 'defined' !== $name) {
            trigger_deprecation('twig/twig', '3.12', 'Creating a "DefinedTest" instance with a test name that is not "defined" is deprecated.');
        }
        parent::__construct($node, $name, $arguments, $lineno);
    }
    public function compile(Compiler $compiler) : void
    {
        $compiler->subcompile($this->getNode('node'));
    }
}
