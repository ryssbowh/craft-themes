<?php 

namespace Ryssbowh\CraftThemesTests\fixtures;

use yii\test\Fixture;

class InstallThemeFixture extends Fixture
{
    public function load()
    {
        codecept_debug('Installing themes');
        \Craft::$app->plugins->installPlugin('child-theme');
        \Craft::$app->plugins->enablePlugin('child-theme');
    }

    public function unload()
    {
        codecept_debug('Uninstalling themes');
        //Not sure if it's a bug or not, but the plugins installed at load() aren't installed any more at this stage, reinstalling it :
        \Craft::$app->plugins->installPlugin('child-theme');
        \Craft::$app->plugins->enablePlugin('child-theme');
        \Craft::$app->plugins->uninstallPlugin('child-theme');
    }
}