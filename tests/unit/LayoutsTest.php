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
use Ryssbowh\CraftThemes\models\layouts\CustomLayout;
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

    public function testCustomLayouts()
    {
        $layout = $this->layouts->createCustom([
            'name' => 'my-layout',
            'elementUid' => 'my-layout',
            'themeHandle' => 'child-theme'
        ]);
        $layout2 = $this->layouts->createCustom([
            'name' => 'my-layout',
            'elementUid' => 'my-layout',
            'themeHandle' => 'parent-theme'
        ]);
        $layout3 = $this->layouts->createCustom([
            'name' => 'my-layout',
            'elementUid' => 'my-layout',
            'themeHandle' => 'child-theme'
        ]);
        $this->assertInstanceOf(CustomLayout::class, $layout);
        $this->assertTrue($this->layouts->save($layout));
        $this->assertCount(25, $this->layouts->all());
        $this->assertInstanceOf(CustomLayout::class, $this->layouts->getCustom('child-theme', 'my-layout'));
        $this->assertTrue($this->layouts->save($layout2));
        $this->assertCount(26, $this->layouts->all());
        $this->assertInstanceOf(CustomLayout::class, $this->layouts->getCustom('parent-theme', 'my-layout'));
        $this->assertFalse($this->layouts->save($layout3));
        $this->assertTrue($this->layouts->deleteCustom($layout));
        $this->assertCount(25, $this->layouts->all());
        $this->assertTrue($this->layouts->deleteCustom($layout2));
        $this->assertCount(24, $this->layouts->all());
        $this->assertNull($this->layouts->getCustom('child-theme', 'my-layout'));
        $this->assertNull($this->layouts->getCustom('parent-theme', 'my-layout'));
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
}
