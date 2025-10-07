<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OCA\Libresign\Vendor\Twig\ExpressionParser;

use OCA\Libresign\Vendor\Twig\Node\Expression\AbstractExpression;
use OCA\Libresign\Vendor\Twig\Parser;
use OCA\Libresign\Vendor\Twig\Token;
/** @internal */
interface PrefixExpressionParserInterface extends ExpressionParserInterface
{
    public function parse(Parser $parser, Token $token) : AbstractExpression;
}
