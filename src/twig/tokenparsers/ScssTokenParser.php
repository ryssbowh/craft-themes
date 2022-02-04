<?php
namespace Ryssbowh\CraftThemes\twig\tokenparsers;

use Ryssbowh\CraftThemes\twig\nodes\BlockCacheNode;
use Ryssbowh\CraftThemes\twig\nodes\ScssNode;
use Twig\Parser;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

class ScssTokenParser extends AbstractTokenParser
{
    /**
     * @return string
     */
    public function getTag(): string
    {
        return 'scss';
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
        $attributes = [
            'force' => false
        ];
        $hasBody = true;

        if ($stream->test(Token::NAME_TYPE, 'file')) {
            $stream->next();
            $nodes['file'] = $parser->getExpressionParser()->parseExpression();
            $hasBody = false;
        }

        if ($stream->test(Token::NAME_TYPE, 'with')) {
            $stream->next();
            $stream->expect(Token::NAME_TYPE, 'options');
            $nodes['options'] = $parser->getExpressionParser()->parseExpression();
        }

        if ($stream->test(Token::NAME_TYPE, 'force')) {
            $stream->next();
            $attributes['force'] = true;
        }

        $stream->expect(Token::BLOCK_END_TYPE);

        if ($hasBody) {
            $nodes['body'] = $parser->subparse([
                $this,
                'decideScssEnd'
            ], true);
            $stream->expect(Token::BLOCK_END_TYPE);
        }

        return new ScssNode($nodes, $attributes, $lineno, $this->getTag());
    }

    /**
     * @param Token $token
     * @return bool
     */
    public function decideScssEnd(Token $token): bool
    {
        return $token->test('endscss');
    }
}
