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

use OCA\Libresign\Vendor\Twig\Attribute\FirstClassTwigCallableReady;
use OCA\Libresign\Vendor\Twig\ExpressionParser\AbstractExpressionParser;
use OCA\Libresign\Vendor\Twig\ExpressionParser\ExpressionParserDescriptionInterface;
use OCA\Libresign\Vendor\Twig\ExpressionParser\InfixAssociativity;
use OCA\Libresign\Vendor\Twig\ExpressionParser\InfixExpressionParserInterface;
use OCA\Libresign\Vendor\Twig\ExpressionParser\PrecedenceChange;
use OCA\Libresign\Vendor\Twig\Node\EmptyNode;
use OCA\Libresign\Vendor\Twig\Node\Expression\AbstractExpression;
use OCA\Libresign\Vendor\Twig\Node\Expression\ConstantExpression;
use OCA\Libresign\Vendor\Twig\Parser;
use OCA\Libresign\Vendor\Twig\Token;
/**
 * @internal
 */
final class FilterExpressionParser extends AbstractExpressionParser implements InfixExpressionParserInterface, ExpressionParserDescriptionInterface
{
    use ArgumentsTrait;
    private $readyNodes = [];
    public function parse(Parser $parser, AbstractExpression $expr, Token $token) : AbstractExpression
    {
        $stream = $parser->getStream();
        $token = $stream->expect(Token::NAME_TYPE);
        $line = $token->getLine();
        if (!$stream->test(Token::OPERATOR_TYPE, '(')) {
            $arguments = new EmptyNode();
        } else {
            $arguments = $this->parseNamedArguments($parser);
        }
        $filter = $parser->getFilter($token->getValue(), $line);
        $ready = \true;
        if (!isset($this->readyNodes[$class = $filter->getNodeClass()])) {
            $this->readyNodes[$class] = (bool) (new \ReflectionClass($class))->getConstructor()->getAttributes(FirstClassTwigCallableReady::class);
        }
        if (!($ready = $this->readyNodes[$class])) {
            trigger_deprecation('twig/twig', '3.12', 'Twig node "%s" is not marked as ready for passing a "TwigFilter" in the constructor instead of its name; please update your code and then add #[FirstClassTwigCallableReady] attribute to the constructor.', $class);
        }
        return new $class($expr, $ready ? $filter : new ConstantExpression($filter->getName(), $line), $arguments, $line);
    }
    public function getName() : string
    {
        return '|';
    }
    public function getDescription() : string
    {
        return 'Twig filter call';
    }
    public function getPrecedence() : int
    {
        return 512;
    }
    public function getPrecedenceChange() : ?PrecedenceChange
    {
        return new PrecedenceChange('twig/twig', '3.21', 300);
    }
    public function getAssociativity() : InfixAssociativity
    {
        return InfixAssociativity::Left;
    }
}
