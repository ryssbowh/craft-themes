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
        $key = \Craft::$app->cache->buildKey($this->buildKey($block));
        $cache = \Craft::$app->cache->get($key);
        return $cache ?: null;
    }

    /**
     * @inheritDoc
     */
    public function setCache(BlockInterface $block, string $data, TagDependency $dep)
    {
        $elems = $this->buildKey($block);
        $elems[] = $block->id;
        \Craft::$app->cache->set(\Craft::$app->cache->buildKey($elems), $data, $this->getDuration(), $dep);
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
     * Get options model
     * 
     * @return BlockStrategyOptions
     */
    abstract protected function getOptionsModel(): string;
}