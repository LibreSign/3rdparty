<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OCA\Libresign\Vendor\Twig\Node\Expression;

use OCA\Libresign\Vendor\Twig\Attribute\FirstClassTwigCallableReady;
use OCA\Libresign\Vendor\Twig\Compiler;
use OCA\Libresign\Vendor\Twig\Node\CoercesChildrenToStringInterface;
use OCA\Libresign\Vendor\Twig\Node\NameDeprecation;
use OCA\Libresign\Vendor\Twig\Node\Node;
use OCA\Libresign\Vendor\Twig\TwigTest;
/** @internal */
class TestExpression extends CallExpression implements ReturnBoolInterface, CoercesChildrenToStringInterface
{
    #[FirstClassTwigCallableReady]
    public function __construct(Node $node, string|TwigTest $test, ?Node $arguments, int $lineno)
    {
        if (!$node instanceof AbstractExpression) {
            trigger_deprecation('twig/twig', '3.15', 'Not passing a "%s" instance to the "node" argument of "%s" is deprecated ("%s" given).', AbstractExpression::class, static::class, $node::class);
        }
        $nodes = ['node' => $node];
        if (null !== $arguments) {
            $nodes['arguments'] = $arguments;
        }
        if ($test instanceof TwigTest) {
            $name = $test->getName();
        } else {
            $name = $test;
            trigger_deprecation('twig/twig', '3.12', 'Not passing an instance of "TwigTest" when creating a "%s" test of type "%s" is deprecated.', $name, static::class);
        }
        parent::__construct($nodes, ['name' => $name, 'type' => 'test'], $lineno);
        if ($test instanceof TwigTest) {
            $this->setAttribute('\OCA\Libresign\vendor\twig_callable', $test);
        }
        $this->deprecateAttribute('arguments', new NameDeprecation('twig/twig', '3.12'));
        $this->deprecateAttribute('callable', new NameDeprecation('twig/twig', '3.12'));
        $this->deprecateAttribute('is_variadic', new NameDeprecation('twig/twig', '3.12'));
        $this->deprecateAttribute('dynamic_name', new NameDeprecation('twig/twig', '3.12'));
    }
    public function compile(Compiler $compiler) : void
    {
        $name = $this->getAttribute('name');
        if ($this->hasAttribute('\OCA\Libresign\vendor\twig_callable')) {
            $name = $this->getAttribute('\OCA\Libresign\vendor\twig_callable')->getName();
            if ($name !== $this->getAttribute('name')) {
                trigger_deprecation('twig/twig', '3.12', 'Changing the value of a "test" node in a NodeVisitor class is not supported anymore.');
                $this->removeAttribute('\OCA\Libresign\vendor\twig_callable');
            }
        }
        if (!$this->hasAttribute('\OCA\Libresign\vendor\twig_callable')) {
            $this->setAttribute('\OCA\Libresign\vendor\twig_callable', $compiler->getEnvironment()->getTest($this->getAttribute('name')));
        }
        $this->compileCallable($compiler);
    }
    public function getStringCoercedChildNames() : array
    {
        $names = [];
        // the `empty` test triggers an implicit string coercion through `CoreExtension::testEmpty()`
        if ('empty' === $this->getAttribute('name')) {
            $names[] = 'node';
        }
        // a test may coerce its arguments to string (the host PHP code is opaque to Twig)
        if ($this->hasNode('arguments')) {
            $names[] = 'arguments';
        }
        return $names;
    }
}
