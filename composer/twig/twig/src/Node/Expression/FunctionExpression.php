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
use OCA\Libresign\Vendor\Twig\Node\NameDeprecation;
use OCA\Libresign\Vendor\Twig\Node\Node;
use OCA\Libresign\Vendor\Twig\TwigFunction;
/** @internal */
class FunctionExpression extends CallExpression implements SupportDefinedTestInterface
{
    use SupportDefinedTestDeprecationTrait;
    use SupportDefinedTestTrait;
    #[FirstClassTwigCallableReady]
    public function __construct(TwigFunction|string $function, Node $arguments, int $lineno)
    {
        if ($function instanceof TwigFunction) {
            $name = $function->getName();
        } else {
            $name = $function;
            trigger_deprecation('twig/twig', '3.12', 'Not passing an instance of "TwigFunction" when creating a "%s" function of type "%s" is deprecated.', $name, static::class);
        }
        parent::__construct(['arguments' => $arguments], ['name' => $name, 'type' => 'function'], $lineno);
        if ($function instanceof TwigFunction) {
            $this->setAttribute('\OCA\Libresign\vendor\twig_callable', $function);
        }
        $this->deprecateAttribute('needs_charset', new NameDeprecation('twig/twig', '3.12'));
        $this->deprecateAttribute('needs_environment', new NameDeprecation('twig/twig', '3.12'));
        $this->deprecateAttribute('needs_context', new NameDeprecation('twig/twig', '3.12'));
        $this->deprecateAttribute('arguments', new NameDeprecation('twig/twig', '3.12'));
        $this->deprecateAttribute('callable', new NameDeprecation('twig/twig', '3.12'));
        $this->deprecateAttribute('is_variadic', new NameDeprecation('twig/twig', '3.12'));
        $this->deprecateAttribute('dynamic_name', new NameDeprecation('twig/twig', '3.12'));
    }
    public function enableDefinedTest() : void
    {
        if ('constant' === $this->getAttribute('name')) {
            $this->definedTest = \true;
        }
    }
    /**
     * @return void
     */
    public function compile(Compiler $compiler)
    {
        $name = $this->getAttribute('name');
        if ($this->hasAttribute('\OCA\Libresign\vendor\twig_callable')) {
            $name = $this->getAttribute('\OCA\Libresign\vendor\twig_callable')->getName();
            if ($name !== $this->getAttribute('name')) {
                trigger_deprecation('twig/twig', '3.12', 'Changing the value of a "function" node in a NodeVisitor class is not supported anymore.');
                $this->removeAttribute('\OCA\Libresign\vendor\twig_callable');
            }
        }
        if (!$this->hasAttribute('\OCA\Libresign\vendor\twig_callable')) {
            $this->setAttribute('\OCA\Libresign\vendor\twig_callable', $compiler->getEnvironment()->getFunction($name));
        }
        if ('constant' === $name && $this->isDefinedTestEnabled()) {
            $this->getNode('arguments')->setNode('checkDefined', new ConstantExpression(\true, $this->getTemplateLine()));
        }
        $this->compileCallable($compiler);
    }
}
