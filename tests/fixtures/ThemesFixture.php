<?php

namespace Ryssbowh\CraftThemesTests\fixtures;

use yii\test\Fixture;

class ThemesFixture extends Fixture
{
    public function load()
    {
        \Craft::$app->plugins->installPlugin('themes', 'pro');
        \Craft::$app->plugins->installPlugin('child-theme');
    }

    public function unload()
    {
        \Craft::$app->plugins->uninstallPlugin('themes');
        \Craft::$app->projectConfig->remove('plugins.themes');
        \Craft::$app->projectConfig->remove('plugins.child-theme');
        \Craft::$app->projectConfig->remove('plugins.parent-theme');
        \Craft::$app->projectConfig->remove('plugins.partial-theme');
    }
}