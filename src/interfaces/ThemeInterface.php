<?php 

namespace Ryssbowh\CraftThemes\interfaces;

/**
 * A theme is a regular plugin, it defines regions and can extend another theme.
 * They can be partials.
 * They define preferences for rendering page elements.
 */
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
     * @return array
     */
    public function getTemplatePaths(): array;

    /**
     * Register this theme's assets in view for a specific path
     *
     * @param string $urlPath
     */
    public function registerAssetBundles(string $urlPath);

    /**
     * Get theme parent
     * 
     * @return ?ThemeInterface
     */
    public function getParent(): ?ThemeInterface;

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
     * Get theme preferences
     * 
     * @return ThemePreferencesInterface
     */
    public function getPreferences(): ThemePreferencesInterface;

    /**
     * Get theme preferences model
     * 
     * @return ThemePreferencesInterface
     */
    public function getPreferencesModel(): ThemePreferencesInterface;

    /**
     * Preview image for the theme, returns a web url
     * 
     * @return string
     */
    public function getPreviewImage(): string;

    /**
     * Does this theme have a preview image
     * 
     * @return bool
     */
    public function getHasPreview(): bool;

    /**
     * Get the base template for rendering regions
     * 
     * @return string
     */
    public function getRegionsTemplate(): string;

    /**
     * Callback after the plugin is uninstalled.
     * At this point the theme data (layouts etc) is uninstalled.
     */
    public function afterThemeUninstall();

    /**
     * Callback after the plugin is installed.
     * At this point the theme data (layouts etc) is installed.
     */
    public function afterThemeInstall();
}