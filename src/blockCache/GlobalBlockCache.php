<?php 

namespace Ryssbowh\CraftThemes\blockCache;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;

class GlobalBlockCache extends BlockCacheStrategy
{
    const CACHE_PREFIX = 'themes.blockCache.global';

    const CACHE_TAG = 'themes.blockCache.global';

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'global';
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Global');
    }

    /**
     * @inheritDoc
     */
    protected function getKey(BlockInterface $block): string
    {
        return 'global';
    }

    /**
     * @inheritDoc
     */
    protected function getKeyPrefix(): string
    {
        return self::CACHE_PREFIX;
    }

    /**
     * @inheritDoc
     */
    protected function getTag(): string
    {
        return self::CACHE_TAG;
    }
}