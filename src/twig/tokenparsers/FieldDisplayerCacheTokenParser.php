<?php
namespace Ryssbowh\CraftThemes\twig\tokenparsers;

use Ryssbowh\CraftThemes\twig\nodes\FieldDisplayerCacheNode;
use Twig\Parser;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

class FieldDisplayerCacheTokenParser extends AbstractTokenParser
{
    /**
     * @return string
     */
    public function getTag(): string
    {
        return 'fielddisplayercache';
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

        return new FieldDisplayerCacheNode($nodes, [], $lineno, $this->getTag());
    }

    /**
     * @param Token $token
     * @return bool
     */
    public function decideCacheEnd(Token $token): bool
    {
        return $token->test('endfielddisplayercache');
    }
}
