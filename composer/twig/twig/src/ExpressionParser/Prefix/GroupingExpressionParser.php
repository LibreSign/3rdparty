<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OCA\Libresign\3rdparty\Twig\ExpressionParser\Prefix;

use OCA\Libresign\3rdparty\Twig\Error\SyntaxError;
use OCA\Libresign\3rdparty\Twig\ExpressionParser\AbstractExpressionParser;
use OCA\Libresign\3rdparty\Twig\ExpressionParser\ExpressionParserDescriptionInterface;
use OCA\Libresign\3rdparty\Twig\ExpressionParser\PrefixExpressionParserInterface;
use OCA\Libresign\3rdparty\Twig\Node\Expression\AbstractExpression;
use OCA\Libresign\3rdparty\Twig\Node\Expression\ListExpression;
use OCA\Libresign\3rdparty\Twig\Node\Expression\Variable\ContextVariable;
use OCA\Libresign\3rdparty\Twig\Parser;
use OCA\Libresign\3rdparty\Twig\Token;
/**
 * @internal
 */
final class GroupingExpressionParser extends AbstractExpressionParser implements PrefixExpressionParserInterface, ExpressionParserDescriptionInterface
{
    public function parse(Parser $parser, Token $token) : AbstractExpression
    {
        $stream = $parser->getStream();
        $expr = $parser->parseExpression($this->getPrecedence());
        if ($stream->nextIf(Token::PUNCTUATION_TYPE, ')')) {
            if (!$stream->test(Token::OPERATOR_TYPE, '=>')) {
                return $expr->setExplicitParentheses();
            }
            return new ListExpression([$expr], $token->getLine());
        }
        // determine if we are parsing an arrow function arguments
        if (!$stream->test(Token::PUNCTUATION_TYPE, ',')) {
            $stream->expect(Token::PUNCTUATION_TYPE, ')', 'An opened parenthesis is not properly closed');
        }
        $names = [$expr];
        while (\true) {
            if ($stream->nextIf(Token::PUNCTUATION_TYPE, ')')) {
                break;
            }
            $stream->expect(Token::PUNCTUATION_TYPE, ',');
            $token = $stream->expect(Token::NAME_TYPE);
            $names[] = new ContextVariable($token->getValue(), $token->getLine());
        }
        if (!$stream->test(Token::OPERATOR_TYPE, '=>')) {
            throw new SyntaxError('A list of variables must be followed by an arrow.', $stream->getCurrent()->getLine(), $stream->getSourceContext());
        }
        return new ListExpression($names, $token->getLine());
    }
    public function getName() : string
    {
        return '(';
    }
    public function getDescription() : string
    {
        return 'Explicit group expression (a)';
    }
    public function getPrecedence() : int
    {
        return 0;
    }
}
