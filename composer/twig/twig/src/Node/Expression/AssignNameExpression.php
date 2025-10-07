<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 * (c) Armin Ronacher
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OCA\Libresign\Vendor\Twig\Node\Expression;

use OCA\Libresign\Vendor\Twig\Compiler;
use OCA\Libresign\Vendor\Twig\Error\SyntaxError;
use OCA\Libresign\Vendor\Twig\Node\Expression\Variable\AssignContextVariable;
use OCA\Libresign\Vendor\Twig\Node\Expression\Variable\ContextVariable;
/** @internal */
class AssignNameExpression extends ContextVariable
{
    public function __construct(string $name, int $lineno)
    {
        if (self::class === static::class) {
            trigger_deprecation('twig/twig', '3.15', 'The "%s" class is deprecated, use "%s" instead.', self::class, AssignContextVariable::class);
        }
        // All names supported by ExpressionParser::parsePrimaryExpression() should be excluded
        if (\in_array(\strtolower($name), ['true', 'false', 'none', 'null'], \true)) {
            throw new SyntaxError(\sprintf('You cannot assign a value to "%s".', $name), $lineno);
        }
        parent::__construct($name, $lineno);
    }
    public function compile(Compiler $compiler) : void
    {
        $compiler->raw('$context[')->string($this->getAttribute('name'))->raw(']');
    }
}
