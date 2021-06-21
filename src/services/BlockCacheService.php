<?php 

namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\events\RegisterBlockCacheStrategies;
use Ryssbowh\CraftThemes\interfaces\BlockCacheStrategyInterface;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use yii\caching\TagDependency;

class BlockCacheService extends Service
{
    const REGISTER_STRATEGIES = 'register_strategies';

    const BLOCK_CACHE_TAG = 'themes.blocks';

    /**
     * @var array
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
     * @return array
     */
    public function getStrategies(): array
    {
        if ($this->_strategies === null) {
            $this->register();
        }
        return $this->_strategies;
    }

    /**
     * Should a block be cached 
     * 
     * @param  BlockInterface $block
     * @return bool
     */
    public function shouldCacheBlock(BlockInterface $block): bool
    {
        return ($this->cacheEnabled and $block->options->cacheStrategy);
    }

    /**
     * Start block cahing
     * 
     * @param  BlockInterface $block
     */
    public function startBlockCaching(BlockInterface $block)
    {
        if ($this->shouldCacheBlock($block)) {
            \Craft::$app->elements->startCollectingCacheTags();
        }
    }

    /**
     * Stop block cahing
     * 
     * @param  BlockInterface $block
     */
    public function stopBlockCaching(BlockInterface $block, $data)
    {
        if ($this->shouldCacheBlock($block)) {
            $dep = \Craft::$app->elements->stopCollectingCacheTags();
            $dep->tags[] = self::BLOCK_CACHE_TAG;
            $this->setBlockCache($block, $data, $dep);
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
    public function getBlockCache(BlockInterface $block): ?string
    {
        if (!$this->shouldCacheBlock($block)) {
            return null;
        }
        return $this->getStrategy($block->options->cacheStrategy)->getCache($block);
    }

    /**
     * Set a block cache
     * 
     * @param  BlockInterface $block
     * @param  string         $data
     */
    public function setBlockCache(BlockInterface $block, string $data, TagDependency $dep)
    {
        if (!$this->shouldCacheBlock($block)) {
            return;
        }
        $this->getStrategy($block->options->cacheStrategy)->setCache($block, $data, $dep);
    }

    /**
     * Flush all block cache
     */
    public function flush()
    {
        TagDependency::invalidate($this->cache, self::BLOCK_CACHE_TAG);
    }

    /**
     * Register cache strategies
     */
    public function register()
    {
        $event = new RegisterBlockCacheStrategies;
        $this->triggerEvent(self::REGISTER_STRATEGIES, $event);
        $this->_strategies = $event->strategies;
    }
}