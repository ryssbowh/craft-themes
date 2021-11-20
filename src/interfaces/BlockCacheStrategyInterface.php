<?php
namespace Ryssbowh\CraftThemes\interfaces;

use Ryssbowh\CraftThemes\models\blockCacheOptions\BlockCacheStrategyOptions;
use yii\caching\TagDependency;

/**
 * A cache strategy will cache a block differently according to various options
 */
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
     * Get strategy description
     * 
     * @return string
     */
    public function getDescription(): string;

    /**
     * get a block cache data.
     * Returns null if cache is not set.
     * 
     * @param  BlockInterface $block
     * @return string|null
     */
    public function getCache(BlockInterface $block): ?string;

    /**
     * Set a block cache
     * 
     * @param BlockInterface $block
     * @param string         $data
     * @param TagDependency  $dep
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