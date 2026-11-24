<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OCA\Libresign\Vendor\Twig\ExpressionParser\Prefix;

use OCA\Libresign\Vendor\Twig\Error\SyntaxError;
use OCA\Libresign\Vendor\Twig\ExpressionParser\AbstractExpressionParser;
use OCA\Libresign\Vendor\Twig\ExpressionParser\ExpressionParserDescriptionInterface;
use OCA\Libresign\Vendor\Twig\ExpressionParser\PrefixExpressionParserInterface;
use OCA\Libresign\Vendor\Twig\Node\Expression\AbstractExpression;
use OCA\Libresign\Vendor\Twig\Parser;
use OCA\Libresign\Vendor\Twig\Token;
/**
 * Sequences, mappings, and call arguments parse the spread operator themselves; it is invalid anywhere else.
 *
 * @internal
 */
final class SpreadExpressionParser extends AbstractExpressionParser implements PrefixExpressionParserInterface, ExpressionParserDescriptionInterface
{
    public function parse(Parser $parser, Token $token) : AbstractExpression
    {
        throw new SyntaxError('The spread operator can only be used on sequence elements, mapping elements, and call arguments.', $token->getLine(), $parser->getStream()->getSourceContext());
    }
    public function getName() : string
    {
        return '...';
    }
    public function getDescription() : string
    {
        return 'Spread operator';
    }
    public function getPrecedence() : int
    {
        return 512;
    }
}
