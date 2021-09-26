<?php 

namespace Ryssbowh\CraftThemes\interfaces;

use Ryssbowh\CraftThemes\models\Region;
use Ryssbowh\CraftThemes\services\ViewModeService;
use craft\base\Element;
use craft\models\FieldLayout;

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
     * Get layout regions
     * 
     * @return array
     */
    public function getRegions(): array;

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
     * Get element associated to that layout (category group, entry type, User etc)
     * 
     * @return mixed
     */
    public function getElement();

    /**
     * Set element associated to that layout
     * 
     * @return mixed
     */
    public function setElement($element);

    /**
     * Get view modes defined for that layout
     * 
     * @return array
     */
    public function getViewModes(): array;

    /**
     * Get view mode by handle
     * 
     * @param  string $handle
     * @return ?ViewModeInterface
     */
    public function getViewMode(string $handle): ?ViewModeInterface;

    /**
     * View modes setter
     * 
     * @param  array $viewModes
     * @return LayoutInterface
     */
    public function setViewModes(?array $viewModes): LayoutInterface;

    /**
     * Is a view mode handle defined in this layout 
     * 
     * @param  string  $handle
     * @return boolean
     */
    public function hasViewMode(string $handle): bool;

    /**
     * Add a view mode
     * 
     * @param  ViewModeInterface $viewMode
     * @return LayoutInterface
     */
    public function addViewMode(ViewModeInterface $viewMode): LayoutInterface;

    /**
     * Get default view mode
     * 
     * @return ViewModeInterface
     */
    public function getDefaultViewMode(): ViewModeInterface;

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
    public function setBlocks(array $blocks): LayoutInterface;

    /**
     * Add a block
     * 
     * @param  BlockInterface $block
     * @param  string         $region
     * @return LayoutInterface
     */
    public function addBlock(BlockInterface $block, string $region): LayoutInterface;

    /**
     * Get the machine name describing the element associated to this layout
     * 
     * @return string
     */
    public function getElementMachineName(): string;

    /**
     * Get a region by handle.
     * 
     * @param  string       $handle
     * @param  bool         $checkLoaded
     * @return Region
     */
    public function getRegion(string $handle): Region;

    /**
     * Is a region defined
     * 
     * @param  string $handle
     * @return bool
     */
    public function hasRegion(string $handle): bool;

    /**
     * Get a display by handle for a view mode
     * 
     * @param  string $viewMode
     * @param  string $handle
     * @return ?DisplayInterface
     */
    public function getDisplayByHandle(string $viewMode, string $handle): ?DisplayInterface;

    /**
     * Replaces a display in this layout, based on its id.
     * 
     * @param  DisplayInterface $display
     * @return LayoutInterface
     */
    public function replaceDisplay(DisplayInterface $display): LayoutInterface;

    /**
     * Get all craft fields defined on this layout's element
     * 
     * @return array
     */
    public function getCraftFields(): array;

    /**
     * Get layout's element field layout
     * 
     * @return FieldLayout
     */
    public function getFieldLayout(): ?FieldLayout;

    /**
     * Render this layout for an element
     *
     * @param  Element $element
     * @param  string  $viewMode
     * @return string
     */
    public function render(Element $element, string $viewMode = ViewModeService::DEFAULT_HANDLE): string;
}