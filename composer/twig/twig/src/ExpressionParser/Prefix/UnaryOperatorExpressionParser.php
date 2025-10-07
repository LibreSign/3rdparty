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

use OCA\Libresign\3rdparty\Twig\ExpressionParser\AbstractExpressionParser;
use OCA\Libresign\3rdparty\Twig\ExpressionParser\ExpressionParserDescriptionInterface;
use OCA\Libresign\3rdparty\Twig\ExpressionParser\PrecedenceChange;
use OCA\Libresign\3rdparty\Twig\ExpressionParser\PrefixExpressionParserInterface;
use OCA\Libresign\3rdparty\Twig\Node\Expression\AbstractExpression;
use OCA\Libresign\3rdparty\Twig\Node\Expression\Unary\AbstractUnary;
use OCA\Libresign\3rdparty\Twig\Parser;
use OCA\Libresign\3rdparty\Twig\Token;
/**
 * @internal
 */
final class UnaryOperatorExpressionParser extends AbstractExpressionParser implements PrefixExpressionParserInterface, ExpressionParserDescriptionInterface
{
    public function __construct(
        /** @var class-string<AbstractUnary> */
        private string $nodeClass,
        private string $name,
        private int $precedence,
        private ?PrecedenceChange $precedenceChange = null,
        private ?string $description = null,
        private array $aliases = []
    )
    {
    }
    /**
     * @return AbstractUnary
     */
    public function parse(Parser $parser, Token $token) : AbstractExpression
    {
        return new $this->nodeClass($parser->parseExpression($this->precedence), $token->getLine());
    }
    public function getName() : string
    {
        return $this->name;
    }
    public function getDescription() : string
    {
        return $this->description ?? '';
    }
    public function getPrecedence() : int
    {
        return $this->precedence;
    }
    public function getPrecedenceChange() : ?PrecedenceChange
    {
        return $this->precedenceChange;
    }
    public function getAliases() : array
    {
        return $this->aliases;
    }
}
