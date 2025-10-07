<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OCA\Libresign\3rdparty\Twig\Node\Expression;

use OCA\Libresign\3rdparty\Twig\Compiler;
use OCA\Libresign\3rdparty\Twig\Node\Expression\Variable\TemplateVariable;
/**
 * Represents a macro call node.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @internal
 */
class MacroReferenceExpression extends AbstractExpression implements SupportDefinedTestInterface
{
    use SupportDefinedTestDeprecationTrait;
    use SupportDefinedTestTrait;
    public function __construct(TemplateVariable $template, string $name, AbstractExpression $arguments, int $lineno)
    {
        parent::__construct(['template' => $template, 'arguments' => $arguments], ['name' => $name], $lineno);
    }
    public function compile(Compiler $compiler) : void
    {
        if ($this->definedTest) {
            $compiler->subcompile($this->getNode('template'))->raw('->hasMacro(')->repr($this->getAttribute('name'))->raw(', $context')->raw(')');
            return;
        }
        $compiler->subcompile($this->getNode('template'))->raw('->getTemplateForMacro(')->repr($this->getAttribute('name'))->raw(', $context, ')->repr($this->getTemplateLine())->raw(', $this->getSourceContext())')->raw(\sprintf('->%s', $this->getAttribute('name')))->raw('(...')->subcompile($this->getNode('arguments'))->raw(')');
    }
}
