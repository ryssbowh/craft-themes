<?php
namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\events\RegisterBlockCacheStrategies;
use Ryssbowh\CraftThemes\interfaces\BlockCacheStrategyInterface;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\models\blocks\ContentBlock;
use Ryssbowh\CraftThemes\models\blocks\CurrentUserBlock;
use yii\caching\TagDependency;

class BlockCacheService extends Service
{
    const REGISTER_STRATEGIES = 'register_strategies';

    const BLOCK_CACHE_TAG = 'themes::blocks';

    /**
     * @var BlockCacheStrategyInterface[]
     */
    protected $_strategies;

    /**
     * @var boolean
     */
    public $cacheEnabled;

    /**
     * @var CacheInterface
     */
    public $cache;

    /**
     * Get all strategies
     * 
     * @return BlockCacheStrategyInterface[]
     */
    public function getStrategies(): array
    {
        if ($this->_strategies === null) {
            $this->register();
        }
        return $this->_strategies;
    }

    /**
     * Start block caching
     * 
     * @param BlockInterface $block
     */
    public function startCaching(BlockInterface $block)
    {
        if ($this->shouldCache($block)) {
            \Craft::info("Starting block cache for block {$block->machineName} ({$block->id})", __METHOD__);
            \Craft::$app->elements->startCollectingCacheTags();
        }
    }

    /**
     * Stop block caching
     * 
     * @param BlockInterface $block
     */
    public function stopCaching(BlockInterface $block, $data)
    {
        if ($this->shouldCache($block)) {
            $dep = \Craft::$app->elements->stopCollectingCacheTags();
            $dep->tags = array_merge(
                $dep->tags,
                [self::BLOCK_CACHE_TAG, self::BLOCK_CACHE_TAG . '::' . $block->id]
            );
            \Craft::info("Stopping block cache for block {$block->machineName} ({$block->id}), deps : " . json_encode($dep->tags), __METHOD__);
            $this->setCache($block, $data, $dep);
        }
    }

    /**
     * Get a strategy by handle
     * 
     * @param  string $handle
     * @return BlockCacheStrategyInterface
     * @throws BlockCacheException
     */
    public function getStrategy(string $handle): BlockCacheStrategyInterface
    {
        if (!isset($this->strategies[$handle])) {
            throw BlockCacheException::noStrategy($handle);
        }
        return $this->strategies[$handle];
    }

    /**
     * Does a strategy handle exist
     * 
     * @param  string  $handle
     * @return boolean
     */
    public function hasStrategy(string $handle): bool
    {
        return isset($this->strategies[$handle]);
    }

    /**
     * Get a block cache
     * 
     * @param  BlockInterface $block
     * @return ?string
     */
    public function getCache(BlockInterface $block): ?string
    {
        if (!$this->shouldCache($block)) {
            return null;
        }
        return $block->cacheStrategy->getCache($block);
    }

    /**
     * Flush all block cache
     */
    public function flush()
    {
        TagDependency::invalidate($this->cache, self::BLOCK_CACHE_TAG);
    }

    /**
     * Should a block be cached 
     * 
     * @param  BlockInterface $block
     * @return bool
     */
    protected function shouldCache(BlockInterface $block): bool
    {
        return ($this->cacheEnabled and $block->canBeCached and $block->cacheStrategy);
    }

    /**
     * Register cache strategies
     */
    protected function register()
    {
        $event = new RegisterBlockCacheStrategies;
        $this->triggerEvent(self::REGISTER_STRATEGIES, $event);
        $this->_strategies = $event->strategies;
    }

    /**
     * Set a block cache
     * 
     * @param BlockInterface $block
     * @param string         $data
     */
    protected function setCache(BlockInterface $block, string $data, TagDependency $dep)
    {
        if (!$this->shouldCache($block)) {
            return;
        }
        $block->cacheStrategy->setCache($block, $data, $dep);
    }
}