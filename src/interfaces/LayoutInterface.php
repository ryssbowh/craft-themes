<?php 

namespace Ryssbowh\CraftThemes\interfaces;

use Ryssbowh\CraftThemes\models\Region;
use Ryssbowh\CraftThemes\services\ViewModeService;
use craft\base\Element;

interface LayoutInterface
{
    /**
     * Eager load layout fields
     * 
     * @param  Element $element
     * @param  string  $viewMode
     */
    public function eagerLoadFields(Element $element, string $viewMode);

    /**
     * Can this layout have urls, for entries or categories for example
     * or not, for users or tags for example.
     * Layouts that can have urls can be assigned blocks, others can't
     * 
     * @return bool
     */
    public function canHaveUrls(): bool;

    /**
     * Can this layout define displays.
     * Only the default layout can't have displays since it doesn't have an element associated to it.
     * 
     * @return bool
     */
    public function hasDisplays(): bool;

    /**
     * Get this layout's theme
     * 
     * @return ThemeInterface
     */
    public function getTheme(): ThemeInterface;

    /**
     * Get project config
     * 
     * @return array
     */
    public function getConfig(): array;

    /**
     * Get description
     * 
     * @return string
     */
    public function getDescription(): string;

    /**
     * Get element associated to that layout
     * 
     * @return mixed
     */
    public function element();

    /**
     * Get view modes defined for that layout
     * 
     * @return array
     */
    public function getViewModes(): array;

    /**
     * View modes setter
     * 
     * @param  array $viewModes
     * @return LayoutInterface
     */
    public function setViewModes(?array $viewModes): LayoutInterface;

    /**
     * Blocks getter
     * 
     * @return array
     */
    public function getBlocks(): array;

    /**
     * Blocks setter
     * 
     * @param  array $blocks
     * @return LayoutInterface
     */
    public function setBlocks(?array $blocks): LayoutInterface;

    /**
     * Add a block
     * 
     * @param  BlockInterface $block
     * @return LayoutInterface
     */
    public function addBlock(BlockInterface $block): LayoutInterface;

    /**
     * Get the machine name describing the element associated to this layout
     * 
     * @return string
     */
    public function getElementMachineName(): string;

    /**
     * Load blocks from database.
     * If this layout doesn't define blocks it will load its blocks from the default layout
     * 
     * @param  boolean $force
     * @return LayoutInterface
     */
    public function loadBlocks(bool $force = false): LayoutInterface;

    /**
     * Get a region by handle.
     *
     * 
     * @param  string       $handle
     * @param  bool         $checkLoaded
     * @return Region
     */
    public function getRegion(string $handle, bool $checkLoaded = true): Region;

    /**
     * Find a block by machine name
     * 
     * @param  string $machineName
     * @return ?BlockInterface
     */
    public function findBlock(string $machineName): ?BlockInterface;

    /**
     * Get all displays for a view mode
     * 
     * @return array
     */
    public function getDisplays(?string $viewMode = null): array;

    /**
     * Displays setter
     * 
     * @param array $displays
     */
    public function setDisplays(array $displays);

    /**
     * Replaces a display in this layout, based on its id.
     * 
     * @param  DisplayInterface $display
     */
    public function replaceDisplay(DisplayInterface $display);

    /**
     * Get all visible displays
     * 
     * @return array
     */
    public function getVisibleDisplays(string $viewMode = ViewModeService::DEFAULT_HANDLE): array;

    /**
     * Get all craft fields defined on this layout's element
     * 
     * @return array
     */
    public function getCraftFields(): array;

    /**
     * get handle
     * 
     * @return string
     */
    public function getHandle(): string;

    /**
     * Render this layout for an element
     *
     * @param  Element $element
     * @param  string  $viewMode
     * @return string
     */
    public function render(Element $element, string $viewMode = ViewModeService::DEFAULT_HANDLE): string;

    /**
     * Get rendering mode. Mode can be either regions or displays
     * 
     * @return string
     */
    public function getRenderingMode(): string;

    /**
     * Set rendering mode to regions
     */
    public function setRegionsRenderingMode();

    /**
     * Set rendering mode to displays
     */
    public function setDisplaysRenderingMode();
}