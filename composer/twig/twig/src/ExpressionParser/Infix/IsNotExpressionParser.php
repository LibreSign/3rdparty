<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OCA\Libresign\Vendor\Twig\ExpressionParser\Infix;

use OCA\Libresign\Vendor\Twig\Node\Expression\AbstractExpression;
use OCA\Libresign\Vendor\Twig\Node\Expression\Unary\NotUnary;
use OCA\Libresign\Vendor\Twig\Parser;
use OCA\Libresign\Vendor\Twig\Token;
/**
 * @internal
 */
final class IsNotExpressionParser extends IsExpressionParser
{
    public function parse(Parser $parser, AbstractExpression $expr, Token $token) : AbstractExpression
    {
        return new NotUnary(parent::parse($parser, $expr, $token), $token->getLine());
    }
    public function getName() : string
    {
        return 'is not';
    }
}
