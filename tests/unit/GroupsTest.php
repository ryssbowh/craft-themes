<?php

use Codeception\Test\Unit;
use Craft;
use Ryssbowh\CraftThemesTests\fixtures\InstallThemeFixture;
use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\services\DisplayService;
use Ryssbowh\CraftThemes\services\LayoutService;
use UnitTester;
use yii\base\Event;

class GroupsTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    public function _fixtures()
    {
        return [
            'themes' => InstallThemeFixture::class
        ];
    }

    protected function _before()
    {
        $this->groups = Themes::getInstance()->groups;
        $this->layouts = Themes::getInstance()->layouts;
        $this->displays = Themes::getInstance()->displays;
    }

    public function testCreatingGroup()
    {
        $layout = $this->layouts->get('child-theme', LayoutService::USER_HANDLE);
        $viewMode = $layout->getViewMode('default');
        $display = $viewMode->displays[0];
        $group = $this->displays->create([
            'type' => DisplayService::TYPE_GROUP,
            'viewMode' => $layout->getViewMode('default'),
            'item' => [
                'name' => 'Test',
                'handle' => 'test'
            ]
        ]);
        $group->item->displays = [$display];
        $viewMode->displays = [$group];
        $this->assertTrue($this->layouts->save($layout));
        $this->assertCount(1, $viewMode->displays);
        $this->assertCount(1, $viewMode->displays[0]->item->displays);
        $this->assertEquals(DisplayService::TYPE_GROUP, $viewMode->displays[0]->type);
        $this->assertEquals(DisplayService::TYPE_FIELD, $viewMode->displays[0]->item->displays[0]->type);
        $this->assertEquals($group->item->displays[0]->group_id, $group->item->id);
        //3 displays : 1 UserInfo for the two themes, plus the group just created
        $this->assertCount(3, $this->displays->all());
    }
}
