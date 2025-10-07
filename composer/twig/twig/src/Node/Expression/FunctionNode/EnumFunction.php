<?php

namespace OCA\Libresign\3rdparty\Twig\Node\Expression\FunctionNode;

use OCA\Libresign\3rdparty\Twig\Compiler;
use OCA\Libresign\3rdparty\Twig\Error\SyntaxError;
use OCA\Libresign\3rdparty\Twig\Node\Expression\ConstantExpression;
use OCA\Libresign\3rdparty\Twig\Node\Expression\FunctionExpression;
/** @internal */
class EnumFunction extends FunctionExpression
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
            throw new SyntaxError('The first argument of the "enum" function must be a string.', $this->getTemplateLine(), $this->getSourceContext());
        }
        if (!\enum_exists($value)) {
            throw new SyntaxError(\sprintf('The first argument of the "enum" function must be the name of an enum, "%s" given.', $value), $this->getTemplateLine(), $this->getSourceContext());
        }
        if (!($cases = $value::cases())) {
            throw new SyntaxError(\sprintf('The first argument of the "enum" function must be a non-empty enum, "%s" given.', $value), $this->getTemplateLine(), $this->getSourceContext());
        }
        $compiler->raw(\sprintf('%s::%s', $value, $cases[0]->name));
    }
}
