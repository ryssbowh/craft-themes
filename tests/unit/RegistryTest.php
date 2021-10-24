<?php

use Codeception\Test\Unit;
use Craft;
use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\events\InstallThemeEvent;
use Ryssbowh\CraftThemes\events\UninstallThemeEvent;
use Ryssbowh\CraftThemes\exceptions\ThemeException;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use Ryssbowh\CraftThemes\services\ThemesRegistry;
use Ryssbowh\CraftThemesTests\themes\child\Theme as ChildTheme;
use Ryssbowh\CraftThemesTests\themes\parent\Theme as ParentTheme;
use Ryssbowh\CraftThemesTests\themes\partial\Theme as PartialTheme;
use UnitTester;
use yii\base\Event;

class RegistryTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    protected $registry;
    protected $plugins;

    protected function _before()
    {
        $this->registry = Themes::getInstance()->registry;
        $this->plugins = Craft::$app->plugins;
    }

    public function testInstallChildTheme()
    {
        $this->installChildTheme();
        $this->assertTrue($this->plugins->isPluginInstalled('parent-theme'));
        $this->assertTrue($this->plugins->isPluginInstalled('partial-theme'));
        $this->assertTrue($this->plugins->isPluginInstalled('child-theme'));
        $this->assertTrue($this->plugins->isPluginEnabled('child-theme'));
        $this->assertTrue($this->plugins->isPluginEnabled('parent-theme'));
        $this->assertTrue($this->plugins->isPluginEnabled('partial-theme'));
    }

    public function testDisablePartialTheme()
    {
        $this->installChildTheme();
        $this->plugins->disablePlugin('partial-theme');
        $this->assertFalse($this->plugins->isPluginEnabled('parent-theme'));
        $this->assertFalse($this->plugins->isPluginEnabled('child-theme'));
        $this->assertFalse($this->plugins->isPluginEnabled('partial-theme'));
    }

    public function testUninstallPartialTheme()
    {
        $this->installChildTheme();
        $this->uninstallPartialTheme();
        $this->assertFalse($this->plugins->isPluginInstalled('child-theme'));
        $this->assertFalse($this->plugins->isPluginInstalled('parent-theme'));
        $this->assertFalse($this->plugins->isPluginInstalled('child-theme'));
        $_this = $this;
        $this->tester->expectThrowable(ThemeException::class, function () use ($_this) {
            $_this->registry->getTheme('child-theme');
        });
    }

    public function testGetThemes()
    {
        $this->installChildTheme();
        $this->assertInstanceOf(ThemeInterface::class, $this->registry->getTheme('child-theme'));
        $this->assertInstanceOf(ThemeInterface::class, $this->registry->getTheme('parent-theme'));
        $this->assertCount(3, $this->registry->all());
        $this->assertCount(2, $this->registry->getNonPartials());
        $this->assertInstanceOf(ParentTheme::class, $this->registry->getTheme('parent-theme'));
        $this->assertContains('Parent Theme', $this->registry->getAsNames());
        $this->assertInstanceOf(ChildTheme::class, $this->registry->getTheme('child-theme'));
        $this->assertContains('Child Theme', $this->registry->getAsNames());
        $this->assertInstanceOf(PartialTheme::class, $this->registry->getTheme('partial-theme'));
        $this->assertContains('Partial Theme', $this->registry->getAsNames());
    }

    protected function installChildTheme()
    {
        $this->tester->expectEvent(ThemesRegistry::class, ThemesRegistry::EVENT_AFTER_INSTALL_THEME, function () {
            Craft::$app->plugins->installPlugin('child-theme');
        });
    }

    protected function uninstallPartialTheme()
    {
        $this->tester->expectEvent(ThemesRegistry::class, ThemesRegistry::EVENT_AFTER_UNINSTALL_THEME, function () {
            Craft::$app->plugins->uninstallPlugin('partial-theme');
        });
    }
}
