<?php

use Codeception\Test\Unit;
use Craft;
use Ryssbowh\CraftThemesTests\fixtures\ThemesFixture;
use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\ViewModeException;
use Ryssbowh\CraftThemes\models\ViewMode;
use Ryssbowh\CraftThemes\models\fields\UserInfo;
use Ryssbowh\CraftThemes\services\LayoutService;
use Ryssbowh\CraftThemes\services\ViewModeService;
use UnitTester;
use craft\db\Query;
use craft\db\Table;
use craft\test\TestSetup;

class ViewModesTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    public function _fixtures()
    {
        return [
            'themes' => ThemesFixture::class
        ];
    }

    protected function _before()
    {
        $this->layouts = Themes::getInstance()->layouts;
        $this->viewModes = Themes::getInstance()->viewModes;
    }

    /**
     * Sections : 3
     * Category groups : 1
     * Globals : 1
     * Volumes: 1
     * Tags : 1
     * User : 1
     * Default : 1
     * Product: 1
     * Variant: 1
     *
     * Layouts per non-partial theme : 11
     * 2 non-partial theme : 22 layouts
     * 1 view mode per layout
     */
    public function testViewModesAreCreated()
    {
        $this->assertCount(22, $this->viewModes->all);
    }

    public function testDefaultViewModes()
    {
        $layout = $this->layouts->get('child-theme', 'user');
        $viewMode = $this->viewModes->getDefault($layout);
        $viewModes = $layout->viewModes;
        $this->assertCount(1, $viewModes);
        $this->assertInstanceOf(ViewMode::class, $viewModes[0]);
        $this->assertEquals($viewMode, $viewModes[0]);
        $this->assertEquals($viewMode->handle, ViewModeService::DEFAULT_HANDLE);

        $layout = $this->layouts->get('child-theme', 'default');
        $viewMode = $this->viewModes->getDefault($layout);
        $viewModes = $layout->viewModes;
        $this->assertCount(1, $viewModes);
        $this->assertInstanceOf(ViewMode::class, $viewModes[0]);
        $this->assertEquals($viewMode, $viewModes[0]);
        $this->assertEquals($viewMode->handle, 'default');
    }

    public function testCreatingDeletingViewModes()
    {
        $entryType = \Craft::$app->sections->getSectionByHandle('single')->entryTypes[0];
        $layout = $this->layouts->get('child-theme', 'entry', $entryType->uid);
        $viewMode = $this->viewModes->create([
            'name' => 'Name',
            'handle' => 'handle',
            'layout' => $layout
        ]);
        $this->assertInstanceOf(ViewMode::class, $viewMode);
        $this->assertTrue($this->viewModes->save($viewMode));
        $this->assertCount(23, $this->viewModes->all);
        $this->assertCount(2, $layout->viewModes);
        $this->assertTrue($this->viewModes->delete($viewMode));
        $this->assertCount(22, $this->viewModes->all);
        $this->assertCount(1, $layout->viewModes);

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

        //Try saving view mode with default handle
        $viewMode = $this->viewModes->create([
            'name' => 'Name',
            'handle' => ViewModeService::DEFAULT_HANDLE,
            'layout' => $layout
        ]);
        $this->tester->expectThrowable(ViewModeException::class, function () use ($_this, $viewMode) {
            $_this->viewModes->save($viewMode);
        });

        //Try deleting default view mode
        $viewMode = $layout->defaultViewMode;
        $this->tester->expectThrowable(ViewModeException::class, function () use ($_this, $viewMode) {
            $_this->viewModes->delete($viewMode);
        });
    }
}
