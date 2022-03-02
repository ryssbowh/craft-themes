<?php
namespace Ryssbowh\CraftThemes\interfaces;

use Ryssbowh\CraftThemes\services\ViewModeService;
use Twig\Markup;
use craft\base\Element;
use craft\base\Field;
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
     * Parent getter
     * 
     * @return ?LayoutInterface
     */
    public function getParent(): ?LayoutInterface;

    /**
     * Parent setter
     * 
     * @param LayoutInterface $layout
     */
    public function setParent(LayoutInterface $layout);

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
     * @return RegionInterface[]
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
     * @return ViewModeInterface[]
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
     * @param  null|ViewModeInterface[] $viewModes
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
     * @return BlockInterface[]
     */
    public function getBlocks(): array;

    /**
     * Blocks setter
     * 
     * @param  BlockInterface[] $blocks
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
     * Get the portion of template name associated with this element.
     * This would be 'blog-article' for an entry type 'article' of a section 'blog'.
     * Or 'authors' for a category group 'authors'
     * Etc
     * 
     * @return string
     */
    public function getTemplatingKey(): string;

    /**
     * Get a region by handle.
     * 
     * @param  string       $handle
     * @param  bool         $checkLoaded
     * @return Region
     */
    public function getRegion(string $handle): RegionInterface;

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
     * @return Field[]
     */
    public function getCraftFields(): array;

    /**
     * Get layout's element field layout
     * 
     * @return FieldLayout
     */
    public function getFieldLayout(): ?FieldLayout;

    /**
     * Get the url to edit displays for a view mode.
     * Returns null if the layout doesn't have displays.
     * Takes the default view mode if $viewMode is null
     * 
     * @param  ViewModeInterface|string|null $viewMode
     * @return ?string
     */
    public function getEditDisplaysUrl($viewMode = null): ?string;

    /**
     * Get the url to edit blocks.
     * Returns the blocks url for the default layout if this layout doesn't have blocks
     * 
     * @return string
     */
    public function getEditBlocksUrl(): string;

    /**
     * Get available templates
     * 
     * @param  ViewModeInterface $viewMode
     * @return array
     */
    public function getTemplates(ViewModeInterface $viewMode): array;

    /**
     * Get available templates for a block
     * 
     * @param  BlockInterface $block
     * @return array
     */
    public function getBlockTemplates(BlockInterface $block): array;

    /**
     * Get available templates for a region
     * 
     * @param  RegionInterface $region
     * @return array
     */
    public function getRegionTemplates(RegionInterface $region): array;

    /**
     * Get available templates for a field
     * 
     * @param  FieldInterface $field
     * @return array
     */
    public function getFieldTemplates(FieldInterface $field): array;

    /**
     * Get available templates for a file
     * 
     * @param  FieldInterface         $field
     * @param  FileDisplayerInterface $displayer
     * @return array
     */
    public function getFileTemplates(FieldInterface $field, FileDisplayerInterface $displayer): array;

    /**
     * Get available templates for a group
     * 
     * @param  GroupInterface $group
     * @return array
     */
    public function getGroupTemplates(GroupInterface $group): array;

    /**
     * Render this layout for an element
     *
     * @param  Element                   $element
     * @param  string|ViewModeInterface  $viewMode
     * @return Markup
     */
    public function render(Element $element, $viewMode = ViewModeService::DEFAULT_HANDLE): Markup;

    /**
     * Render this layout's regions
     * 
     * @return Markup
     */
    public function renderRegions(): Markup;
}