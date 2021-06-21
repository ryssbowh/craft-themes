<?php 

namespace Ryssbowh\CraftThemes\blockCache;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;

class PageBlockCache extends BlockCacheStrategy
{
    const CACHE_PREFIX = 'themes.blockCache.page';
    
    const CACHE_TAG = 'themes.blockCache.page';

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'page';
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'User');
    }

    /**
     * @inheritDoc
     */
    protected function getKey(BlockInterface $block): string
    {
        if ($user = \Craft::$app->user) {
            return $user->getIdentity()->id;
        }
        return 0;
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