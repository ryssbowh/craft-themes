<?php 

namespace Ryssbowh\CraftThemes\blockCache;

use Ryssbowh\CraftThemes\interfaces\BlockCacheStrategyInterface;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\models\BlockCacheStrategyOptions;
use craft\base\Component;
use yii\caching\TagDependency;

abstract class BlockCacheStrategy extends Component implements BlockCacheStrategyInterface
{
    protected $_options;

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function fields()
    {
        return ['handle', 'name', 'description'];
    }

    /**
     * @inheritDoc
     */
    public function getCache(BlockInterface $block): ?string
    {
        $cache = \Craft::$app->cache->get($this->buildKey($block));
        return $cache ?: null;
    }

    /**
     * @inheritDoc
     */
    public function setCache(BlockInterface $block, string $data, TagDependency $dep)
    {
        $dep->tags[] = $this->getTag();
        \Craft::$app->cache->set($this->buildKey($block), $data, null, $dep);
    }

    /**
     * @inheritDoc
     */
    public function flush()
    {
        TagDependency::invalidate(\Craft::$app->cache, $this->getTag());
    }

    /**
     * @inheritDoc
     */
    public function getOptions(): BlockCacheStrategyOptions
    {
        if ($this->_options === null) {
            $this->_options = $this->getOptionsModel();
        }
        return $this->_options;
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): BlockCacheStrategyOptions
    {
        return new BlockCacheStrategyOptions;
    }

    /**
     * Builds a cache key for a block
     * 
     * @param  BlockInterface $block
     * @return string
     */
    protected function buildKey(BlockInterface $block): string
    {
        return \Craft::$app->cache->buildKey([$this->getKeyPrefix(), $block->id, $this->getKey($block)]);
    }

    /**
     * Get the cache key specific to this strategy
     * 
     * @param  BlockInterface $block
     * @return string
     */
    abstract protected function getKey(BlockInterface $block): string;

    /**
     * Get the cache key prefix specific to this strategy
     * 
     * @return string
     */
    abstract protected function getKeyPrefix(): string;

    /**
     * Get this strategy cache dependency tag
     * 
     * @return string
     */
    abstract protected function getTag(): string;
}