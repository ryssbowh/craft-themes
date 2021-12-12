<?php
namespace Ryssbowh\CraftThemes\blockCache;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\models\BlockStrategyOptions;
use Ryssbowh\CraftThemes\models\blockCacheOptions\GlobalOptions;

/**
 * This strategy will cache blocks differently for each url path.
 * It has options to cache differently for each user, for guests/non guests or for each view port (mobile, tablet, desktop)
 */
class PathBlockCache extends GlobalBlockCache
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
    public function buildKey(BlockInterface $block): array
    {
        $key = [self::CACHE_TAG, 'path-' . \Craft::$app->request->getFullPath()];
        if ($this->options->cachePerAuthenticated) {
            $key[] = \Craft::$app->user ? 'auth' : 'noauth';
        }
        if ($this->options->cachePerViewport) {
            $key[] = 'view-port-' . $this->getViewPort();
        }
        if ($this->options->cachePerUser and $user = \Craft::$app->user) {
            $key[] = 'user-id-' . $user->getIdentity()->id;
        }
        if ($this->options->cachePerSite) {
            $site = \Craft::$app->sites->getCurrentSite();
            $key[] = 'site-' . $site->id;
        }
        return $key;
    }
}