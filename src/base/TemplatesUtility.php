<?php
namespace Ryssbowh\CraftThemes\base;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\assets\UtilitiesAssets;
use craft\base\Utility;

/**
 * @since 4.2.0
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
    public static function iconPath(): ?string
    {
        return \Craft::getAlias('@Ryssbowh/CraftThemes/icon-mask-grey.svg');
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
