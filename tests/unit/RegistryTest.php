<?php

use Codeception\Test\Unit;
use Craft;
use Ryssbowh\CraftThemesTests\themes\child\Theme as ChildTheme;
use Ryssbowh\CraftThemesTests\themes\parent\Theme as ParentTheme;
use Ryssbowh\CraftThemesTests\themes\partial\Theme as PartialTheme;
use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\events\InstallThemeEvent;
use Ryssbowh\CraftThemes\events\UninstallThemeEvent;
use Ryssbowh\CraftThemes\exceptions\ThemeException;
use Ryssbowh\CraftThemes\helpers\PluginsHelper;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use Ryssbowh\CraftThemes\services\ThemesRegistry;
use UnitTester;
use yii\base\Event;

class RegistryTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    protected function _before()
    {
        $this->plugins = Craft::$app->plugins;
    }

    public function testInstallTheme()
    {
        PluginsHelper::$readFromYaml = false;
        $this->plugins->installPlugin('themes', 'pro');
        $this->plugins->installPlugin('child-theme');
        $this->assertTrue($this->plugins->isPluginInstalled('parent-theme'));
        $this->assertTrue($this->plugins->isPluginInstalled('partial-theme'));
        $this->assertTrue($this->plugins->isPluginInstalled('child-theme'));
        $this->assertTrue($this->plugins->isPluginEnabled('child-theme'));
        $this->assertTrue($this->plugins->isPluginEnabled('parent-theme'));
        $this->assertTrue($this->plugins->isPluginEnabled('partial-theme'));
        $config = \Craft::$app->projectConfig->get('plugins.themes.themesInstalled');
        $this->assertEquals($config, ['partial-theme', 'parent-theme', 'child-theme']);
        $this->plugins->uninstallPlugin('themes');
        \Craft::$app->projectConfig->remove('plugins.themes');
        \Craft::$app->projectConfig->remove('plugins.child-theme');
        \Craft::$app->projectConfig->remove('plugins.parent-theme');
        \Craft::$app->projectConfig->remove('plugins.partial-theme');
    }

    public function testUninstallTheme()
    {
        PluginsHelper::$readFromYaml = false;
        Craft::$app->plugins->installPlugin('themes', 'pro');
        Craft::$app->plugins->installPlugin('child-theme');
        Craft::$app->plugins->uninstallPlugin('themes');
        $this->assertFalse($this->plugins->isPluginInstalled('child-theme'));
        $this->assertFalse($this->plugins->isPluginInstalled('parent-theme'));
        $this->assertFalse($this->plugins->isPluginInstalled('child-theme'));
        $config = \Craft::$app->projectConfig->get('plugins.themes.themesInstalled');
        $this->assertEquals($config, null);
    }

    public function testDisableTheme()
    {
        $this->plugins->installPlugin('themes');
        $this->plugins->installPlugin('child-theme');
        $this->plugins->disablePlugin('partial-theme');
        $this->assertFalse($this->plugins->isPluginEnabled('parent-theme'));
        $this->assertFalse($this->plugins->isPluginEnabled('child-theme'));
        $this->assertFalse($this->plugins->isPluginEnabled('partial-theme'));
        $_this = $this;
        $this->tester->expectThrowable(ThemeException::class, function () use ($_this) {
            $_this->registry()->getTheme('child-theme');
        });
        $this->plugins->enablePlugin('child-theme');
        $this->assertTrue($this->plugins->isPluginEnabled('parent-theme'));
        $this->assertTrue($this->plugins->isPluginEnabled('child-theme'));
        $this->assertTrue($this->plugins->isPluginEnabled('partial-theme'));
        $this->plugins->uninstallPlugin('themes');
    }

    public function testGetThemes()
    {
        $this->plugins->installPlugin('themes');
        $this->plugins->installPlugin('child-theme');
        $this->assertInstanceOf(ThemeInterface::class, $this->registry()->getTheme('child-theme'));
        $this->assertInstanceOf(ThemeInterface::class, $this->registry()->getTheme('parent-theme'));
        $this->assertCount(3, $this->registry()->all);
        $this->assertCount(2, $this->registry()->getNonPartials());
        $this->assertInstanceOf(ParentTheme::class, $this->registry()->getTheme('parent-theme'));
        $this->assertContains('Parent Theme', $this->registry()->getAsNames());
        $this->assertInstanceOf(ChildTheme::class, $this->registry()->getTheme('child-theme'));
        $this->assertContains('Child Theme', $this->registry()->getAsNames());
        $this->assertInstanceOf(PartialTheme::class, $this->registry()->getTheme('partial-theme'));
        $this->assertContains('Partial Theme', $this->registry()->getAsNames());
        $this->plugins->uninstallPlugin('themes');
    }

    public function testChangeEdition()
    {
        $this->plugins->installPlugin('themes');
        $this->plugins->installPlugin('child-theme');
        $this->plugins->switchEdition('themes', 'pro');
        $config = \Craft::$app->projectConfig->get('plugins.themes.themesInstalled');
        $this->assertEquals($config, ['partial-theme', 'parent-theme', 'child-theme']);
        $this->plugins->uninstallPlugin('themes');
    }

    protected function registry()
    {
        return Themes::$plugin->registry;
    }
}
