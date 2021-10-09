<?php
namespace Ryssbowh\CraftThemes\blockCache;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\base\BlockCacheStrategy;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\models\BlockCacheStrategyOptions;
use Ryssbowh\CraftThemes\models\blockCacheOptions\GlobalOptions;

/**
 * This strategy will cache blocks differently for each url path.
 * It has options to cache differently for each user, for guests/non guests or for each view port (mobile, tablet, desktop)
 */
class PathBlockCache extends BlockCacheStrategy
{
    const CACHE_TAG = 'themes.blockCache.path';

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'path';
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Url path');
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return \Craft::t('themes', 'Block will be cached differently for each url regardless of query string (what comes after ?)');
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
        $key = [\Craft::$app->request->getFullPath()];
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