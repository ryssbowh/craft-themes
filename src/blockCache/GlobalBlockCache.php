<?php 

namespace Ryssbowh\CraftThemes\blockCache;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\models\BlockCacheStrategyOptions;
use Ryssbowh\CraftThemes\models\blockCacheOptions\GlobalOptions;

class GlobalBlockCache extends BlockCacheStrategy
{
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
    public function getOptionsModel(): BlockCacheStrategyOptions
    {
        return new GlobalOptions;
    }

    /**
     * @inheritDoc
     */
    protected function getKey(BlockInterface $block): string
    {
        $key = [];
        if ($this->options->cachePerAuthenticated) {
            $key[] = \Craft::$app->user ? 'auth' : 'noauth';
        }
        if ($this->options->cachePerUser and $user = \Craft::$app->user) {
            $key[] = $user->getIdentity()->id;
        }
        return implode('-', $key);
    }

    /**
     * @inheritDoc
     */
    protected function getKeyPrefix(): string
    {
        return self::CACHE_TAG;
    }

    /**
     * @inheritDoc
     */
    protected function getTag(): string
    {
        return self::CACHE_TAG;
    }
}