<?php
namespace Ryssbowh\CraftThemes\twig\tokenparsers;

use Ryssbowh\CraftThemes\twig\nodes\BlockCacheNode;
use Twig\Parser;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

class BlockCacheTokenParser extends AbstractTokenParser
{
    /**
     * @return string
     */
    public function getTag(): string
    {
        return 'blockcache';
    }

    /**
     * @inheritdoc
     */
    public function parse(Token $token)
    {
        $lineno = $token->getLine();
        /** @var Parser $parser */
        $parser = $this->parser;
        $stream = $parser->getStream();

        $nodes = [];

        $stream->expect(Token::BLOCK_END_TYPE);
        $nodes['body'] = $parser->subparse([
            $this,
            'decideCacheEnd'
        ], true);
        $stream->expect(Token::BLOCK_END_TYPE);

        return new BlockCacheNode($nodes, [], $lineno, $this->getTag());
    }

    /**
     * @param Token $token
     * @return bool
     */
    public function decideCacheEnd(Token $token): bool
    {
        return $token->test('endblockcache');
    }
}
