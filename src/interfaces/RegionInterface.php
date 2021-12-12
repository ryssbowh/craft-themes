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
     * @return BlockInterface[]
     */
    public function getBlocks(): array;

    /**
     * Get visible blocks
     * 
     * @return BlockInterface[]
     */
    public function getVisibleBlocks(): array;

    /**
     * Does this region have blocks
     * 
     * @return boolean
     */
    public function hasBlocks(): bool;

    /**
     * Does this region have visible blocks
     * 
     * @return boolean
     */
    public function hasVisibleBlocks(): bool;

    /**
     * Blocks setter
     * 
     * @param BlockInterface[]|null $blocks
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
     * @return string[]
     */
    public function getTemplates(LayoutInterface $layout): array;

    /**
     * Callback before rendering, returning false will skip the region rendering
     * 
     * @return boolean
     */
    public function beforeRender(): bool;

    /**
     * Render this region
     * 
     * @return string
     */
    public function render(): string;
}