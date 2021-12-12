<?php
namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\interfaces\DisplayerInterface;
use Ryssbowh\CraftThemes\interfaces\FieldDisplayerInterface;
use Ryssbowh\CraftThemes\interfaces\FileDisplayerInterface;
use Ryssbowh\CraftThemes\interfaces\ViewModeInterface;
use yii\caching\CacheInterface;
use yii\caching\TagDependency;

class DisplayerCacheService extends Service
{
    const DISPLAYER_CACHE_TAG = 'themes::displayers';
    const EAGERLOAD_CACHE_TAG = 'themes::eagerLoad';
    const VIEWMODE_CACHE_TAG = 'themes::viewMode';

    /**
     * @var boolean
     */
    public $cacheEnabled;

    /**
     * @var CacheInterface
     */
    public $cache;

    /**
     * Start displayer caching
     * 
     * @param DisplayerInterface $displayer
     */
    public function startCaching(DisplayerInterface $displayer)
    {
        if ($this->shouldCache($displayer)) {
            \Craft::info("Starting displayer cache for displayer {$displayer->handle}", __METHOD__);
            \Craft::$app->elements->startCollectingCacheTags();
        }
    }

    /**
     * Stop displayer caching
     * 
     * @param DisplayerInterface $displayer
     * @param string $data
     * @param mixed $cachePrefix
     */
    public function stopCaching(DisplayerInterface $displayer, $data, $cachePrefix)
    {
        if ($this->shouldCache($displayer)) {
            $dep = \Craft::$app->elements->stopCollectingCacheTags();
            $element = Themes::$plugin->view->renderingElement;
            $dep->tags = array_unique(array_merge(
                $dep->tags,
                $element->getCacheTags(),
                [
                    BlockCacheService::BLOCK_CACHE_TAG,
                    BlockCacheService::BLOCK_CACHE_TAG . '::' . $this->contentBlock->id,
                    'element::' . get_class($element) . '::' . $element->id, 
                    self::DISPLAYER_CACHE_TAG,
                    self::DISPLAYER_CACHE_TAG . '::' . $displayer->field->id,
                    self::VIEWMODE_CACHE_TAG . '::' . $displayer->field->viewMode->id
                ]
            ));
            \Craft::info("Stopping displayer cache for displayer {$displayer->handle}, deps : " . json_encode($dep->tags), __METHOD__);
            $this->setCache($displayer, $data, $dep, $cachePrefix);
        }
    }

    /**
     * Returns all the fields that can be eager loaded on a view mode and stores it in cache
     * 
     * @param  ViewModeInterface $viewMode
     * @return string[]
     */
    public function eagerLoadViewMode(ViewModeInterface $viewMode): array
    {
        if (!$this->cacheEnabled) {
            return $viewMode->eagerLoad();
        }
        $key = self::EAGERLOAD_CACHE_TAG . '::' . $viewMode->id;
        $cached = $this->cache->get($key);
        if ($cached === false) {
            $cached = $viewMode->eagerLoad();
            $dep = new TagDependency([
                'tags' => [
                    self::VIEWMODE_CACHE_TAG . '::' . $viewMode->id,
                    self::DISPLAYER_CACHE_TAG
                ]
            ]);
            $this->cache->set($key, $cached, null, $dep);
        }
        return $cached;
    }

    /**
     * Get a displayer cache
     * 
     * @param  DisplayerInterface $displayer
     * @param  mixed $cachePrefix
     * @return ?string
     */
    public function getCache(DisplayerInterface $displayer, $cachePrefix): ?string
    {
        if (!$this->shouldCache($displayer)) {
            return null;
        }
        $key = $this->buildKey($displayer, $cachePrefix);
        return $this->cache->get($key) ?: null;
    }

    /**
     * Flush all displayer cache
     */
    public function flush()
    {
        TagDependency::invalidate($this->cache, self::DISPLAYER_CACHE_TAG);
    }

    /**
     * Should a displayer be cached 
     * 
     * @param  DisplayerInterface $displayer
     * @return bool
     */
    protected function shouldCache(DisplayerInterface $displayer): bool
    {
        return ($this->cacheEnabled and $this->contentBlock->cacheStrategy and $displayer->canBeCached);
    }

    /**
     * Set a displayer cache
     * 
     * @param DisplayerInterface $displayer
     * @param mixed $cachePrefix
     * @param string         $data
     */
    protected function setCache(DisplayerInterface $displayer, string $data, TagDependency $dep, $cachePrefix)
    {
        if (!$this->shouldCache($displayer)) {
            return;
        }
        $key = $this->buildKey($displayer, $cachePrefix);
        $this->cache->set($key, $data, null, $dep);
    }

    /**
     * Build a key to cache a displayer
     * 
     * @param  DisplayerInterface $displayer
     * @param  mixed $cachePrefix
     * @return string
     */
    protected function buildKey(DisplayerInterface $displayer, $cachePrefix): string
    {
        $key = array_merge(
            $this->contentBlock->cacheStrategy->buildKey($this->contentBlock),
            [$displayer::CACHE_PREFIX . '-' .$cachePrefix]
        );
        return $this->cache->buildKey($key);
    }

    /**
     * Get the content block
     * 
     * @return BlockInterface
     */
    protected function getContentBlock(): BlockInterface
    {
        return Themes::$plugin->view->renderingBlock;
    }
}