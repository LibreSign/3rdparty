<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OCA\Libresign\3rdparty\Twig\ExpressionParser\Infix;

use OCA\Libresign\3rdparty\Twig\Node\Expression\AbstractExpression;
use OCA\Libresign\3rdparty\Twig\Node\Expression\Unary\NotUnary;
use OCA\Libresign\3rdparty\Twig\Parser;
use OCA\Libresign\3rdparty\Twig\Token;
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
