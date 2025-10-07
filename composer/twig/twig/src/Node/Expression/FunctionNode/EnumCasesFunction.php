<?php

namespace OCA\Libresign\Vendor\Twig\Node\Expression\FunctionNode;

use OCA\Libresign\Vendor\Twig\Compiler;
use OCA\Libresign\Vendor\Twig\Error\SyntaxError;
use OCA\Libresign\Vendor\Twig\Node\Expression\ConstantExpression;
use OCA\Libresign\Vendor\Twig\Node\Expression\FunctionExpression;
/** @internal */
class EnumCasesFunction extends FunctionExpression
{
    public function compile(Compiler $compiler) : void
    {
        $arguments = $this->getNode('arguments');
        if ($arguments->hasNode('enum')) {
            $firstArgument = $arguments->getNode('enum');
        } elseif ($arguments->hasNode('0')) {
            $firstArgument = $arguments->getNode('0');
        } else {
            $firstArgument = null;
        }
        if (!$firstArgument instanceof ConstantExpression || 1 !== \count($arguments)) {
            parent::compile($compiler);
            return;
        }
        $value = $firstArgument->getAttribute('value');
        if (!\is_string($value)) {
            throw new SyntaxError('The first argument of the "enum_cases" function must be a string.', $this->getTemplateLine(), $this->getSourceContext());
        }
        if (!\enum_exists($value)) {
            throw new SyntaxError(\sprintf('The first argument of the "enum_cases" function must be the name of an enum, "%s" given.', $value), $this->getTemplateLine(), $this->getSourceContext());
        }
        $compiler->raw(\sprintf('%s::cases()', $value));
    }
}
