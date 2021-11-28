<?php
namespace Ryssbowh\CraftThemes\base;

use Ryssbowh\CraftThemes\interfaces\BlockCacheStrategyInterface;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\models\BlockStrategyOptions;
use craft\base\Component;
use yii\caching\TagDependency;

/**
 * Base class for all block cache strategies
 */
abstract class BlockCacheStrategy extends Component implements BlockCacheStrategyInterface
{
    /**
     * @var BlockCacheStrategyOptions
     */
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
        return ['handle', 'name', 'description', 'options'];
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
        \Craft::$app->cache->delete($this->getTag());
        TagDependency::invalidate(\Craft::$app->cache, $this->getTag());
    }

    /**
     * @inheritDoc
     */
    public function getOptions(): BlockStrategyOptions
    {
        if ($this->_options === null) {
            $class = $this->getOptionsModel();
            $this->_options = new $class;
        }
        return $this->_options;
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): BlockStrategyOptions
    {
        return new BlockStrategyOptions;
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