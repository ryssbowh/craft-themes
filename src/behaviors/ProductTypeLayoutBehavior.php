<?php
namespace Ryssbowh\CraftThemes\behaviors;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use Ryssbowh\CraftThemes\services\LayoutService;
use craft\commerce\models\ProductType;
use yii\base\Behavior;

/**
 * This behaviour is attached to all the variants
 */
class ProductTypeLayoutBehavior extends Behavior
{
    /**
     * @var ProductType
     */
    public $owner;

    /**
     * Layout getter
     * 
     * @param  string|ThemeInterface|null $theme theme instance or theme handle, or null for the current theme
     * @return ?LayoutInterface
     */
    public function getLayout($theme = null): ?LayoutInterface
    {
        if (is_null($theme)) {
            $theme = Themes::$plugin->registry->current;
        }
        return Themes::$plugin->layouts->get($theme, LayoutService::PRODUCT_HANDLE, $this->owner->uid);
    }

    /**
     * Variant layout getter
     * 
     * @param  string|ThemeInterface|null $theme theme instance or theme handle, or null for the current theme
     * @return ?LayoutInterface
     */
    public function getVariantLayout($theme = null): ?LayoutInterface
    {
        if (is_null($theme)) {
            $theme = Themes::$plugin->registry->current;
        }
        return Themes::$plugin->layouts->get($theme, LayoutService::VARIANT_HANDLE, $this->owner->uid);
    }

    /**
     * Get the url to edit displays for a theme and a view mode.
     * If the theme is null the current theme will be used.
     * 
     * @param  ThemeInterface|string|null    $theme
     * @param  ViewModeInterface|string|null $viewMode
     * @return ?string
     */
    public function getEditDisplaysUrl($theme = null, $viewMode = null): ?string
    {
        return $this->getLayout($theme)->getEditDisplaysUrl($viewMode);
    }

    /**
     * Get the url to edit blocks for a theme.
     * If the theme is null the current theme will be used.
     * 
     * @param  ThemeInterface|string|null    $theme
     * @return string
     */
    public function getEditBlocksUrl($theme = null): string
    {
        return $this->getLayout($theme)->getEditBlocksUrl();
    }

    /**
     * Get the url to edit displays for a theme and a view mode.
     * If the theme is null the current theme will be used.
     * 
     * @param  ThemeInterface|string|null    $theme
     * @param  ViewModeInterface|string|null $viewMode
     * @return ?string
     */
    public function getEditVariantDisplaysUrl($theme = null, $viewMode = null): ?string
    {
        return $this->getVariantLayout($theme)->getEditDisplaysUrl($viewMode);
    }
}
