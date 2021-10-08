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
use Ryssbowh\CraftThemes\exceptions\ViewModeException;
use Ryssbowh\CraftThemes\models\ViewMode;
use Ryssbowh\CraftThemes\models\fields\UserInfo;
use Ryssbowh\CraftThemes\services\LayoutService;
use Ryssbowh\CraftThemes\services\ViewModeService;
use UnitTester;

class ViewModesTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    protected $displays;
    protected $layouts;

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
        $this->viewModes = Themes::getInstance()->viewModes;
    }

    public function testViewModesAreCreated()
    {
        $this->assertCount(24, $this->viewModes->all());
    }

    public function testUserLayoutHasDefaultViewMode()
    {
        $layout = $this->layouts->get('child-theme', LayoutService::USER_HANDLE);
        $viewMode = $this->viewModes->getDefault($layout);
        $viewModes = $layout->viewModes;
        $this->assertCount(1, $viewModes);
        $this->assertInstanceOf(ViewMode::class, $viewModes[0]);
        $this->assertEquals($viewMode, $viewModes[0]);
        $this->assertEquals($viewMode->handle, ViewModeService::DEFAULT_HANDLE);
    }

    public function testCreatingViewModes()
    {
        $fixture = $this->tester->grabFixture('sections');
        $entryType = $fixture->getSection(0)->entryTypes[0];
        $layout = $this->layouts->get('child-theme', LayoutService::ENTRY_HANDLE, $entryType->uid);
        $viewMode = $this->viewModes->create([
            'name' => 'Name',
            'handle' => 'handle',
            'layout' => $layout
        ]);
        $this->assertInstanceOf(ViewMode::class, $viewMode);
        $this->assertTrue($this->viewModes->save($viewMode));
        $this->assertCount(25, $this->viewModes->all());
        $this->assertCount(2, $layout->viewModes);

        $viewMode = $this->viewModes->create([
            'name' => 'Name',
            'handle' => 'handle2'
        ]);
        $layout->addViewMode($viewMode);
        $this->assertTrue($this->layouts->save($layout));
        $this->assertCount(26, $this->viewModes->all());
        $this->assertCount(3, $layout->viewModes);

        //Try saving duplicated view mode
        $viewMode = $this->viewModes->create([
            'name' => 'Name',
            'handle' => 'handle',
            'layout' => $this->layouts->getDefault('child-theme')
        ]);
        $_this = $this;
        $this->tester->expectThrowable(ViewModeException::class, function () use ($_this, $viewMode) {
            $this->viewModes->save($viewMode);
        });
    }

    public function testDeletingViewModes()
    {
        $_this = $this;
        $fixture = $this->tester->grabFixture('sections');
        $entryType = $fixture->getSection(0)->entryTypes[0];
        $layout = $this->layouts->get('child-theme', LayoutService::ENTRY_HANDLE, $entryType->uid);
        $viewMode = $this->viewModes->create([
            'name' => 'Name',
            'handle' => 'handle',
            'layout' => $layout
        ]);
        $this->viewModes->save($viewMode);
        $this->assertTrue($this->viewModes->delete($viewMode));
        $this->assertCount(24, $this->viewModes->all());
        $this->assertCount(1, $layout->viewModes);

        $viewMode = $this->viewModes->create([
            'name' => 'Name',
            'handle' => ViewModeService::DEFAULT_HANDLE,
            'layout' => $layout
        ]);
        $this->tester->expectThrowable(ViewModeException::class, function () use ($_this, $viewMode) {
            $_this->viewModes->save($viewMode);
        });

        $viewMode = $layout->defaultViewMode;
        $this->tester->expectThrowable(ViewModeException::class, function () use ($_this, $viewMode) {
            $_this->viewModes->delete($viewMode);
        });
    }
}
