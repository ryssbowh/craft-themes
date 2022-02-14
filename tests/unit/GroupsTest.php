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

    protected function _before()
    {
        \Craft::$app->plugins->installPlugin('child-theme');
        $this->groups = Themes::getInstance()->groups;
        $this->layouts = Themes::getInstance()->layouts;
        $this->displays = Themes::getInstance()->displays;
    }

    public function testCreatingGroup()
    {
        $layout = $this->layouts->get('child-theme', LayoutService::USER_HANDLE);
        $viewMode = $layout->getViewMode('default');
        $display = $viewMode->displays[0];
        $groupDisplay = $this->displays->create([
            'type' => DisplayService::TYPE_GROUP,
            'viewMode' => $layout->getViewMode('default'),
            'item' => [
                'name' => 'Test',
                'handle' => 'test'
            ]
        ]);
        $viewMode->displays = [$groupDisplay];
        $group = $groupDisplay->item;
        $group->displays = [$display];
        $this->assertTrue($this->layouts->save($layout));
        //we added one display (the group) and removed one, so the total stays the same in the view mode
        $this->assertCount(1, $viewMode->displays);
        $this->assertCount(1, $group->displays);
        $this->assertEquals(DisplayService::TYPE_GROUP, $viewMode->displays[0]->type);
        $this->assertEquals(DisplayService::TYPE_FIELD, $viewMode->displays[0]->item->displays[0]->type);
        $this->assertEquals($group->displays[0]->group_id, $group->id);
    }
}
