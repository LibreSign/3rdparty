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

use OCA\Libresign\3rdparty\Twig\Error\SyntaxError;
use OCA\Libresign\3rdparty\Twig\ExpressionParser\AbstractExpressionParser;
use OCA\Libresign\3rdparty\Twig\ExpressionParser\ExpressionParserDescriptionInterface;
use OCA\Libresign\3rdparty\Twig\ExpressionParser\InfixAssociativity;
use OCA\Libresign\3rdparty\Twig\ExpressionParser\InfixExpressionParserInterface;
use OCA\Libresign\3rdparty\Twig\Lexer;
use OCA\Libresign\3rdparty\Twig\Node\Expression\AbstractExpression;
use OCA\Libresign\3rdparty\Twig\Node\Expression\ArrayExpression;
use OCA\Libresign\3rdparty\Twig\Node\Expression\ConstantExpression;
use OCA\Libresign\3rdparty\Twig\Node\Expression\GetAttrExpression;
use OCA\Libresign\3rdparty\Twig\Node\Expression\MacroReferenceExpression;
use OCA\Libresign\3rdparty\Twig\Node\Expression\NameExpression;
use OCA\Libresign\3rdparty\Twig\Node\Expression\Variable\TemplateVariable;
use OCA\Libresign\3rdparty\Twig\Parser;
use OCA\Libresign\3rdparty\Twig\Template;
use OCA\Libresign\3rdparty\Twig\Token;
/**
 * @internal
 */
final class DotExpressionParser extends AbstractExpressionParser implements InfixExpressionParserInterface, ExpressionParserDescriptionInterface
{
    use ArgumentsTrait;
    public function parse(Parser $parser, AbstractExpression $expr, Token $token) : AbstractExpression
    {
        $stream = $parser->getStream();
        $token = $stream->getCurrent();
        $lineno = $token->getLine();
        $arguments = new ArrayExpression([], $lineno);
        $type = Template::ANY_CALL;
        if ($stream->nextIf(Token::OPERATOR_TYPE, '(')) {
            $attribute = $parser->parseExpression();
            $stream->expect(Token::PUNCTUATION_TYPE, ')');
        } else {
            $token = $stream->next();
            if ($token->test(Token::NAME_TYPE) || $token->test(Token::NUMBER_TYPE) || $token->test(Token::OPERATOR_TYPE) && \preg_match(Lexer::REGEX_NAME, $token->getValue())) {
                $attribute = new ConstantExpression($token->getValue(), $token->getLine());
            } else {
                throw new SyntaxError(\sprintf('Expected name or number, got value "%s" of type %s.', $token->getValue(), $token->toEnglish()), $token->getLine(), $stream->getSourceContext());
            }
        }
        if ($stream->test(Token::OPERATOR_TYPE, '(')) {
            $type = Template::METHOD_CALL;
            $arguments = $this->parseCallableArguments($parser, $token->getLine());
        }
        if ($expr instanceof NameExpression && (null !== $parser->getImportedSymbol('template', $expr->getAttribute('name')) || '_self' === $expr->getAttribute('name') && $attribute instanceof ConstantExpression)) {
            return new MacroReferenceExpression(new TemplateVariable($expr->getAttribute('name'), $expr->getTemplateLine()), 'macro_' . $attribute->getAttribute('value'), $arguments, $expr->getTemplateLine());
        }
        return new GetAttrExpression($expr, $attribute, $arguments, $type, $lineno);
    }
    public function getName() : string
    {
        return '.';
    }
    public function getDescription() : string
    {
        return 'Get an attribute on a variable';
    }
    public function getPrecedence() : int
    {
        return 512;
    }
    public function getAssociativity() : InfixAssociativity
    {
        return InfixAssociativity::Left;
    }
}
