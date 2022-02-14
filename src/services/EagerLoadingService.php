<?php
namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\interfaces\ViewModeInterface;
use yii\caching\CacheInterface;
use yii\caching\TagDependency;

class EagerLoadingService extends Service
{
    const EAGERLOAD_CACHE_TAG = 'themes::eagerLoad';

    /**
     * @var bool
     */
    public $cacheEnabled;

    /**
     * @var CacheInterface
     */
    public $cache;

    /**
     * Returns all the fields that can be eager loaded on a view mode and stores it in cache
     * 
     * @param  ViewModeInterface $viewMode
     * @return string[]
     */
    public function getEagerLoadable(ViewModeInterface $viewMode): array
    {
        if (!$this->cacheEnabled) {
            return $viewMode->eagerLoad();
        }
        $key = self::EAGERLOAD_CACHE_TAG . '::' . $viewMode->id;
        $eagerLoadable = $this->cache->get($key);
        if ($eagerLoadable === false) {
            $dependencies = [];
            $eagerLoadable = $viewMode->eagerLoad('', 0, $dependencies);
            $dep = new TagDependency([
                'tags' => array_merge($dependencies, [
                    ViewModeService::VIEWMODE_CACHE_TAG . '::' . $viewMode->id,
                    DisplayerCacheService::DISPLAYER_CACHE_TAG,
                    self::EAGERLOAD_CACHE_TAG
                ])
            ]);
            $this->cache->set($key, $eagerLoadable, null, $dep);
        }
        return $eagerLoadable;
    }

    /**
     * Flush eager loading cache
     */
    public function flushCache()
    {
        TagDependency::invalidate($this->cache, self::EAGERLOAD_CACHE_TAG);
    }
}