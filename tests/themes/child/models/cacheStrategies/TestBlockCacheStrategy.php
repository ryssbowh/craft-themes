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

    protected function getKey(BlockInterface $block): string
    {
        return 'test-' . $block->id;
    }

    protected function getKeyPrefix(): string
    {
        return 'test';
    }

    protected function getTag(): string
    {
        return 'test';
    }
}