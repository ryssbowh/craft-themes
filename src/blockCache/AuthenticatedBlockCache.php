<?php 

namespace Ryssbowh\CraftThemes\blockCache;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;

class AuthenticatedBlockCache extends BlockCacheStrategy
{
    const CACHE_PREFIX = 'themes.blockCache.auth';

    const CACHE_TAG = 'themes.blockCache.auth';

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'authenticated';
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Authenticated');
    }

    /**
     * @inheritDoc
     */
    protected function getKey(BlockInterface $block): string
    {
        return \Craft::$app->user ? 'auth' : 'noauth';
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