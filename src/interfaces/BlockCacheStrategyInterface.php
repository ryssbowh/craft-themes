<?php 

namespace Ryssbowh\CraftThemes\interfaces;

use Ryssbowh\CraftThemes\models\BlockCacheStrategyOptions;
use yii\caching\TagDependency;

interface BlockCacheStrategyInterface
{
    /**
     * Get strategy handle
     * 
     * @return string
     */
    public function getHandle(): string;

    /**
     * Get strategy name
     * 
     * @return string
     */
    public function getName(): string;

    /**
     * get a block cache data.
     * Returns null if cache is not set.
     * 
     * @param  BlockInterface $block
     * @return null
     */
    public function getCache(BlockInterface $block): ?string;

    /**
     * Set a block cache
     * 
     * @param BlockInterface $block
     * @param TagDependency  $dep
     * @param string         $data
     */
    public function setCache(BlockInterface $block, string $data, TagDependency $dep);

    /**
     * Flush cache
     */
    public function flush();

    /**
     * Get options model (populated)
     * 
     * @return BlockCacheStrategyOptions
     */
    public function getOptions(): BlockCacheStrategyOptions;

    /**
     * Get options model
     * 
     * @return BlockCacheStrategyOptions
     */
    public function getOptionsModel(): BlockCacheStrategyOptions;
}