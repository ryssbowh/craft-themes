<?php
namespace Ryssbowh\CraftThemes\traits;

use Ryssbowh\CraftThemes\assets\SlickAssets;

/**
 * Trait for displayers that display a slick carousel
 */
trait SlickRenderer
{
    /**
     * @inheritDoc
     */
    public function beforeRender(&$value): bool
    {
        \Craft::$app->view->registerAssetBundle(SlickAssets::class);
        return true;
    }
}