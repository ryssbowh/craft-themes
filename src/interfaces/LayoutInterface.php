<?php
namespace Ryssbowh\CraftThemes\interfaces;

use Ryssbowh\CraftThemes\models\Region;
use Ryssbowh\CraftThemes\services\ViewModeService;
use craft\base\Element;
use craft\models\FieldLayout;

/**
 * A layout is associated to a theme and unless it's a custom layout, to a Craft element.
 * Those elements can be : entry type, category group, global set, volume, tag group or user layout.
 * Layouts have regions, as defined by their theme.
 */
interface LayoutInterface
{
    const RENDER_MODE_DISPLAYS = 'displays';
    const RENDER_MODE_REGIONS = 'regions';

    /**
     * Type getter
     * 
     * @return string
     */
    public function getType(): string;

    /**
     * Eager load layout fields
     * 
     * @param Element            $element
     * @param ViewModeInterface  $viewMode
     */
    public function eagerLoadFields(Element $element, ViewModeInterface $viewMode);

    /**
     * Can this layout be assigned blocks
     * 
     * @return bool
     */
    public function canHaveBlocks(): bool;

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