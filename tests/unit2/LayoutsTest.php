<?php

use Codeception\Test\Unit;
use Craft;
use Ryssbowh\CraftThemesTests\fixtures\CategoryGroupsFixture;
use Ryssbowh\CraftThemesTests\fixtures\GlobalSetsFixture;
use Ryssbowh\CraftThemesTests\fixtures\InstallThemeFixture;
use Ryssbowh\CraftThemesTests\fixtures\SectionsFixture;
use Ryssbowh\CraftThemesTests\fixtures\TagGroupsFixture;
use Ryssbowh\CraftThemesTests\fixtures\VolumesFixture;
use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\LayoutException;
use Ryssbowh\CraftThemes\exceptions\ThemeException;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\layouts\CategoryLayout;
use Ryssbowh\CraftThemes\models\layouts\EntryLayout;
use Ryssbowh\CraftThemes\models\layouts\GlobalLayout;
use Ryssbowh\CraftThemes\models\layouts\TagLayout;
use Ryssbowh\CraftThemes\models\layouts\VolumeLayout;
use Ryssbowh\CraftThemes\services\LayoutService;
use UnitTester;

class LayoutsTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    protected $layouts;
    protected $registry;

    public function _fixtures()
    {
        return [
            'themes' => InstallThemeFixture::class,
            'groups' => CategoryGroupsFixture::class,
            'sections' => SectionsFixture::class,
            'globals' => GlobalSetsFixture::class,
            'volumes' => VolumesFixture::class,
            'tags' => TagGroupsFixture::class
        ];
    }

    protected function _before()
    {
        $this->layouts = Themes::getInstance()->layouts;
        $this->registry = Themes::getInstance()->registry;
    }

    public function testLayoutCounts()
    {
        $this->assertCount(24, $this->layouts->all());
        $this->assertCount(12, $this->layouts->getForTheme('child-theme'));
        $this->assertCount(12, $this->layouts->getForTheme('parent-theme'));
        $this->assertCount(22, $this->layouts->withDisplays());
        $this->assertCount(0, $this->layouts->getForTheme('partial-theme'));
    }

    public function testElementsLayoutAreCreatedAndDeleted()
    {
        $fixture = $this->tester->grabFixture('groups');
        $group = $fixture->getGroup(0);
        $layout = $this->layouts->get('child-theme', LayoutService::CATEGORY_HANDLE, $group->uid);
        $this->assertInstanceOf(CategoryLayout::class, $layout);
        $fixture->deleteGroup(0);
        $layout = $this->layouts->get('child-theme', LayoutService::CATEGORY_HANDLE, $group->uid);
        $this->assertNull($layout);
        $this->assertCount(11, $this->layouts->getForTheme('child-theme'));

        $fixture = $this->tester->grabFixture('sections');
        $section = $fixture->getSection(0);
        $layout = $this->layouts->get('child-theme', LayoutService::ENTRY_HANDLE, $section->entryTypes[0]->uid);
        $this->assertInstanceOf(EntryLayout::class, $layout);
        $fixture->deleteSection(0);
        $layout = $this->layouts->get('child-theme', LayoutService::ENTRY_HANDLE, $section->entryTypes[0]->uid);
        $this->assertNull($layout);
        $this->assertCount(10, $this->layouts->getForTheme('child-theme'));

        $fixture = $this->tester->grabFixture('globals');
        $global = $fixture->getSet(0);
        $layout = $this->layouts->get('child-theme', LayoutService::GLOBAL_HANDLE, $global->uid);
        $this->assertInstanceOf(GlobalLayout::class, $layout);
        $fixture->deleteSet(0);
        $layout = $this->layouts->get('child-theme', LayoutService::GLOBAL_HANDLE, $global->uid);
        $this->assertNull($layout);
        $this->assertCount(9, $this->layouts->getForTheme('child-theme'));

        $fixture = $this->tester->grabFixture('volumes');
        $volume = $fixture->getVolume(0);
        $layout = $this->layouts->get('child-theme', LayoutService::VOLUME_HANDLE, $volume->uid);
        $this->assertInstanceOf(VolumeLayout::class, $layout);
        $fixture->deleteVolume(0);
        $layout = $this->layouts->get('child-theme', LayoutService::VOLUME_HANDLE, $volume->uid);
        $this->assertNull($layout);
        $this->assertCount(8, $this->layouts->getForTheme('child-theme'));

        $fixture = $this->tester->grabFixture('tags');
        $group = $fixture->getGroup(0);
        $layout = $this->layouts->get('child-theme', LayoutService::TAG_HANDLE, $group->uid);
        $this->assertInstanceOf(TagLayout::class, $layout);
        $fixture->deleteGroup(0);
        $layout = $this->layouts->get('child-theme', LayoutService::TAG_HANDLE, $group->uid);
        $this->assertNull($layout);
        $this->assertCount(7, $this->layouts->getForTheme('child-theme'));
    }

    public function testUninstallTheme()
    {
        Craft::$app->plugins->uninstallPlugin('child-theme');
        $this->assertCount(12, $this->layouts->all());
        $this->assertCount(0, $this->layouts->getForTheme('child-theme'));
        $this->assertCount(12, $this->layouts->getForTheme('parent-theme'));
    }

    public function testUninstallPartialTheme()
    {
        Craft::$app->plugins->uninstallPlugin('partial-theme');
        $this->assertCount(0, $this->layouts->all());
    }

    public function testReinstall()
    {
        $this->layouts->install();
        $this->testLayoutCounts();
    }

    public function testValidatingLayout()
    {
        $_this = $this;
        $this->tester->expectThrowable(LayoutException::class, function () use ($_this) {
            $_this->layouts->create([
                'type' => 'wrongType'
            ]);
        });
        $layout = $this->layouts->create([
            'type' => LayoutService::DEFAULT_HANDLE,
            'themeHandle' => 'wrong-theme'
        ]);
        //Theme doesn't exist :
        $this->assertFalse($this->layouts->save($layout));
        $this->assertTrue($layout->hasErrors('themeHandle'));
        //No layouts allowed for partial themes
        $layout->themeHandle = 'partial-theme';
        $this->assertFalse($this->layouts->save($layout));
        $this->assertTrue($layout->hasErrors('themeHandle'));
    }

    public function testSavingDuplicateLayouts()
    {
        $layout = $this->layouts->create([
            'type' => LayoutService::DEFAULT_HANDLE,
            'themeHandle' => 'child-theme'
        ]);
        $_this = $this;
        $this->tester->expectThrowable(LayoutException::class, function () use ($layout, $_this) {
            $_this->layouts->save($layout);
        });
        $layout = $this->layouts->create([
            'type' => LayoutService::USER_HANDLE,
            'themeHandle' => 'child-theme'
        ]);
        $this->tester->expectThrowable(LayoutException::class, function () use ($layout, $_this) {
            $_this->layouts->save($layout);
        });
    }

    public function testDeletingDefaultLayoutsFails()
    {
        $_this = $this;
        $this->tester->expectThrowable(LayoutException::class, function () use ($_this) {
            $default = $_this->layouts->getDefault('child-theme');
            $_this->layouts->delete($default);
        });
        $this->tester->expectThrowable(LayoutException::class, function () use ($_this) {
            $default = $_this->layouts->get('child-theme', LayoutService::USER_HANDLE);
            $_this->layouts->delete($default);
        });
    }
}
