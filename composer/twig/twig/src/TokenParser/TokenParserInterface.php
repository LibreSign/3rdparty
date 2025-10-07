<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OCA\Libresign\3rdparty\Twig\TokenParser;

use OCA\Libresign\3rdparty\Twig\Error\SyntaxError;
use OCA\Libresign\3rdparty\Twig\Node\Node;
use OCA\Libresign\3rdparty\Twig\Parser;
use OCA\Libresign\3rdparty\Twig\Token;
/**
 * Interface implemented by token parsers.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @internal
 */
interface TokenParserInterface
{
    /**
     * Sets the parser associated with this token parser.
     */
    public function setParser(Parser $parser) : void;
    /**
     * Parses a token and returns a node.
     *
     * @return Node
     *
     * @throws SyntaxError
     */
    public function parse(Token $token);
    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string
     */
    public function getTag();
}
