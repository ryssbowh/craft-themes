<?php
namespace Ryssbowh\CraftThemes\base;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\assets\UtilitiesAssets;
use craft\base\Utility;

/**
 * @since 3.3.0
 */
class TemplatesUtility extends Utility
{
    /**
     * @inheritDoc
     */
    public static function displayName (): string
    {
        return \Craft::t('themes', 'Themes templates');
    }

    /**
     * @inheritDoc
     */
    public static function id(): string
    {
        return 'themes-templates';
    }

    /**
     * @inheritDoc
     */
    public static function iconPath ()
    {
        return \Craft::getAlias('@Ryssbowh/CraftThemes/icon.svg');
    }

    /**
     * @inheritDoc
     */
    public static function contentHtml (): string
    {
        \Craft::$app->view->registerAssetBundle(UtilitiesAssets::class);

        return \Craft::$app->view->renderTemplate('themes/cp/utility', [
            'themes' => Themes::$plugin->registry->all,
            'isPro' => Themes::$plugin->is(Themes::EDITION_PRO)
        ]);
    }
}
