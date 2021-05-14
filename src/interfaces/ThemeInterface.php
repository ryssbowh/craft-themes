<?php 

namespace Ryssbowh\CraftThemes\interfaces;

interface ThemeInterface 
{
    /**
     * Which other theme does this extends from.
     * Returns a plugin handle.
     * 
     * @return ?string
     */
    public function getExtends(): ?string;

    /**
     * Folder where the templates are stored for this theme
     * 
     * @return string
     */
    public function getTemplatesFolder(): string;

    /**
     * Absolute template paths, including those of the parent(s)
     * 
     * @return string
     */
    public function getTemplatePaths(): array;

    /**
     * Register this theme's assets in view for a specific path
     */
    public function registerAssetBundles(string $urlPath);

    /**
     * Get theme parent
     * 
     * @return ?ThemeInterface
     */
    public function getParent(): ?ThemeInterface;

    /**
     * Which region the content block should be installed in
     * 
     * @return string
     */
    public function contentBlockRegion(): ?string;

    /**
     * Is this theme partial.
     * Partial themes can't be selected for front end, their purpose is only to be inherited from
     * 
     * @return boolean
     */
    public function isPartial(): bool;

    /**
     * Get an url for an theme asset.
     * 
     * @param  string $path
     * @return string
     */
    public function getAssetUrl(string $path): string;

    /**
     * Get theme's regions
     * 
     * @return array
     */
    public function getRegions(): array;

    /**
     * Callback after the theme has been set for a request
     */
    public function afterSet();

    /**
     * Preview image for the theme, returns a web url
     * 
     * @return string
     */
    public function getPreviewImage(): string;
}