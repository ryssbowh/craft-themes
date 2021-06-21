<?php 

namespace Ryssbowh\CraftThemes\blockCache;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;

class UserBlockCache extends BlockCacheStrategy
{
    const CACHE_PREFIX = 'themes.blockCache.user';
    
    const CACHE_TAG = 'themes.blockCache.user';

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'user';
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