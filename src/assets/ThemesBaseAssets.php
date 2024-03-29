<?php
namespace Ryssbowh\CraftThemes\assets;

use craft\web\AssetBundle;
use craft\web\View;
use craft\web\assets\cp\CpAsset;

abstract class ThemesBaseAssets extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function registerAssetFiles($view)
    {
        parent::registerAssetFiles($view);

        if ($view instanceof View) {
            $this->_registerTranslations($view);
        }
    }

    protected function _registerTranslations($view)
    {
        $messages = require \Craft::getAlias('@Ryssbowh/CraftThemes/translations/en-GB/themes.php');
        $messages = array_keys($messages);
        $view->registerTranslations('themes', $messages);
        $view->registerTranslations('app', [
            'Title',
            'Label',
            'Handle',
            'Content',
            'Global',
            'Entry',
            'User',
            'Users',
            'Template',
            'Active',
            'Asset',
            'Assets',
            'Category',
            'Categories',
            'Site',
            'System',
            'Width',
            'Height',
            'Entry Type',
            'Global Set',
            'Copied to clipboard.',
            'Add an entry',
            'Add an asset',
            'Add a category',
            'Add a user',
            'Tag'
        ]);
    }
}