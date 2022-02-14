<?php
namespace Ryssbowh\CraftThemesTests\themes\child\models\cacheStrategies;

use Ryssbowh\CraftThemes\base\BlockCacheStrategy;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;

class TestBlockCacheStrategy extends BlockCacheStrategy
{
    public function getHandle(): string
    {
        return 'test';
    }

    public function getName(): string
    {
        return 'Test';
    }

    public function buildKey(BlockInterface $block): array
    {
        return ['test-' . $block->id];
    }

    public function getDuration(): ?int
    {
        return null;
    }

    protected function getOptionsModel(): string
    {
        return TestBlockCacheStrategyOptions::class;
    }
}