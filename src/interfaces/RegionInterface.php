<?php 

namespace Ryssbowh\CraftThemes\interfaces;

use craft\base\Element;

/**
 * Regions are defined by themes, and are assigned to layouts. They can have blocks.
 */
interface RegionInterface
{
    /**
     * Blocks getter
     * 
     * @return array
     */
    public function getBlocks(): array;

    /**
     * Blocks setter
     * 
     * @param array $blocks
     */
    public function setBlocks(?array $blocks);

    /**
     * Theme getter
     * 
     * @return ThemeInterface
     */
    public function getTheme(): ThemeInterface;

    /**
     * Add a block to this region
     * 
     * @param BlockInterface $block
     */
    public function addBlock(BlockInterface $block);

    /**
     * Render this region
     * 
     * @return string
     */
    public function render(): string;
}