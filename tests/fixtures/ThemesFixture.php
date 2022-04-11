<?php

namespace Ryssbowh\CraftThemesTests\fixtures;

use Ryssbowh\CraftThemes\helpers\PluginsHelper;
use yii\test\Fixture;

class ThemesFixture extends Fixture
{
    public function load()
    {
        PluginsHelper::$readFromYaml = false;
        \Craft::$app->plugins->installPlugin('themes', 'pro');
        \Craft::$app->plugins->installPlugin('child-theme');
    }

    public function unload()
    {
        PluginsHelper::$readFromYaml = false;
        \Craft::$app->plugins->uninstallPlugin('themes');
        \Craft::$app->projectConfig->remove('plugins.themes');
        \Craft::$app->projectConfig->remove('plugins.child-theme');
        \Craft::$app->projectConfig->remove('plugins.parent-theme');
        \Craft::$app->projectConfig->remove('plugins.partial-theme');
    }
}