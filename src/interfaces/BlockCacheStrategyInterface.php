<?php
namespace Ryssbowh\CraftThemes\interfaces;

use Ryssbowh\CraftThemes\models\BlockStrategyOptions;
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
     * @return ?string
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
     * Get options model (populated)
     * 
     * @return BlockStrategyOptions
     */
    public function getOptions(): BlockStrategyOptions;

    /**
     * Get the cache key elements specific to this strategy
     * 
     * @param  BlockInterface $block
     * @return array
     */
    public function buildKey(BlockInterface $block): array;

    /**
     * Get cache duration (seconds).
     * Returning null will use Yii default caching duration.
     * 0 means infinity.
     * 
     * @return ?int
     */
    public function getDuration(): ?int;
}