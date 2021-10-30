<?php
namespace Ryssbowh\CraftThemes\interfaces;

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
     * Does this region have blocks
     * 
     * @return boolean
     */
    public function hasBlocks(): bool;

    /**
     * Get visible blocks
     * 
     * @return array
     */
    public function getVisibleBlocks(): array;

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
     * Get available templates
     * 
     * @param  LayoutInterface $layout
     * @return array
     */
    public function getTemplates(LayoutInterface $layout): array;

    /**
     * Render this region
     * 
     * @return string
     */
    public function render(): string;
}