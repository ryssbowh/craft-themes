<?php 

namespace Ryssbowh\CraftThemes\interfaces;

use craft\base\Element;

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
     * Render for an element
     * 
     * @return string
     */
    public function render(Element $element): string;
}