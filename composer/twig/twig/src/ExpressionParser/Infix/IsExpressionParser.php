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

use OCA\Libresign\3rdparty\Twig\Attribute\FirstClassTwigCallableReady;
use OCA\Libresign\3rdparty\Twig\ExpressionParser\AbstractExpressionParser;
use OCA\Libresign\3rdparty\Twig\ExpressionParser\ExpressionParserDescriptionInterface;
use OCA\Libresign\3rdparty\Twig\ExpressionParser\InfixAssociativity;
use OCA\Libresign\3rdparty\Twig\ExpressionParser\InfixExpressionParserInterface;
use OCA\Libresign\3rdparty\Twig\Node\Expression\AbstractExpression;
use OCA\Libresign\3rdparty\Twig\Node\Expression\ArrayExpression;
use OCA\Libresign\3rdparty\Twig\Node\Expression\MacroReferenceExpression;
use OCA\Libresign\3rdparty\Twig\Node\Expression\NameExpression;
use OCA\Libresign\3rdparty\Twig\Node\Nodes;
use OCA\Libresign\3rdparty\Twig\Parser;
use OCA\Libresign\3rdparty\Twig\Token;
use OCA\Libresign\3rdparty\Twig\TwigTest;
/**
 * @internal
 */
class IsExpressionParser extends AbstractExpressionParser implements InfixExpressionParserInterface, ExpressionParserDescriptionInterface
{
    use ArgumentsTrait;
    private $readyNodes = [];
    public function parse(Parser $parser, AbstractExpression $expr, Token $token) : AbstractExpression
    {
        $stream = $parser->getStream();
        $test = $parser->getTest($token->getLine());
        $arguments = null;
        if ($stream->test(Token::OPERATOR_TYPE, '(')) {
            $arguments = $this->parseNamedArguments($parser);
        } elseif ($test->hasOneMandatoryArgument()) {
            $arguments = new Nodes([0 => $parser->parseExpression($this->getPrecedence())]);
        }
        if ('defined' === $test->getName() && $expr instanceof NameExpression && null !== ($alias = $parser->getImportedSymbol('function', $expr->getAttribute('name')))) {
            $expr = new MacroReferenceExpression($alias['node']->getNode('var'), $alias['name'], new ArrayExpression([], $expr->getTemplateLine()), $expr->getTemplateLine());
        }
        $ready = $test instanceof TwigTest;
        if (!isset($this->readyNodes[$class = $test->getNodeClass()])) {
            $this->readyNodes[$class] = (bool) (new \ReflectionClass($class))->getConstructor()->getAttributes(FirstClassTwigCallableReady::class);
        }
        if (!($ready = $this->readyNodes[$class])) {
            trigger_deprecation('twig/twig', '3.12', 'Twig node "%s" is not marked as ready for passing a "TwigTest" in the constructor instead of its name; please update your code and then add #[FirstClassTwigCallableReady] attribute to the constructor.', $class);
        }
        return new $class($expr, $ready ? $test : $test->getName(), $arguments, $stream->getCurrent()->getLine());
    }
    public function getPrecedence() : int
    {
        return 100;
    }
    public function getName() : string
    {
        return 'is';
    }
    public function getDescription() : string
    {
        return 'Twig tests';
    }
    public function getAssociativity() : InfixAssociativity
    {
        return InfixAssociativity::Left;
    }
}
