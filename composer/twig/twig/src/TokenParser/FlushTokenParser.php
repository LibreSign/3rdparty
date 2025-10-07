<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OCA\Libresign\Vendor\Twig\TokenParser;

use OCA\Libresign\Vendor\Twig\Node\FlushNode;
use OCA\Libresign\Vendor\Twig\Node\Node;
use OCA\Libresign\Vendor\Twig\Token;
/**
 * Flushes the output to the client.
 *
 * @see flush()
 *
 * @internal
 */
final class FlushTokenParser extends AbstractTokenParser
{
    public function parse(Token $token) : Node
    {
        $this->parser->getStream()->expect(Token::BLOCK_END_TYPE);
        return new FlushNode($token->getLine());
    }
    public function getTag() : string
    {
        return 'flush';
    }
}
