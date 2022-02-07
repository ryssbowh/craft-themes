<?php

use Codeception\Test\Unit;
use Craft;
use Ryssbowh\CraftThemesTests\fixtures\EntriesFixture;
use Ryssbowh\CraftThemesTests\fixtures\InstallThemeFixture;
use Ryssbowh\CraftThemesTests\fixtures\SectionsFixture;
use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\services\DisplayService;
use Ryssbowh\CraftThemes\services\LayoutService;
use UnitTester;
use craft\elements\Entry;
use craft\elements\User;
use yii\base\Event;

class ViewTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    protected function _before()
    {
        \Craft::$app->plugins->installPlugin('child-theme');
        $this->layouts = Themes::getInstance()->layouts;
        $this->view = Themes::getInstance()->view;
        $this->rules = Themes::getInstance()->rules;
        //Setting theme for console requests and re-resolve current theme
        $this->rules->console = 'child-theme';
        $this->rules->setConsole = true;
        $this->rules->resolveCurrentTheme();
        \Craft::$app->view->setTemplateMode('site');
    }

    public function testEagerLoading()
    {
        $section = \Craft::$app->sections->getSectionByHandle('channel');
        $entryTypes = $section->getEntryTypes();
        $entryType = reset($entryTypes);
        $entry = $this->createEntry($section, $entryType);

        $layout = $this->layouts->get('child-theme', 'entry', $entryType->uid);
        $this->assertInstanceOf(LayoutInterface::class, $layout);
        $viewMode = $layout->getDefaultViewMode();
        $with = $viewMode->eagerLoad();
        $this->assertEquals(['author', 'author.photo', 'assets', 'category', 'users', 'users.photo', 'entries', 'image', 'tag'], $with);
    }

    public function testRendering()
    {
        $section = \Craft::$app->sections->getSectionByHandle('channel');
        $entryTypes = $section->getEntryTypes();
        $entryType = reset($entryTypes);
        $entry = $this->createEntry($section, $entryType);

        $layout = $this->layouts->get('child-theme', 'entry', $entryType->uid);
        $this->assertInstanceOf(LayoutInterface::class, $layout);
        $viewMode = $layout->getDefaultViewMode();
        $with = $viewMode->eagerLoad();
        \Craft::$app->elements->eagerLoadElements(get_class($entry), [$entry], $with);
        $html = $this->view->renderLayout($layout, $viewMode, $entry);
        $this->assertStringContainsString('<div class="layout" data-viewmode="default" data-handle="'.$section->handle."-".$entryType->handle.'" data-type="entry">', $html);
        $this->assertStringContainsString("My Entry", $html);
        $this->assertStringContainsString('<div class="display field" data-displayer="title-title" data-field="title">', $html);
        $this->assertStringContainsString('<div class="display field" data-displayer="user-default" data-field="author">', $html);
        $this->assertStringContainsString('<div class="display field" data-displayer="checkboxes-label" data-field="checkboxes">', $html);
        $this->assertStringContainsString('<div class="display field" data-displayer="dropdown-label" data-field="dropdown">', $html);
        $this->assertStringContainsString('<div class="display field" data-displayer="radiobuttons-label" data-field="radioButtons">', $html);
    }

    protected function createEntry($section, $entryType)
    {
        $user = User::find()->one();
        $entry = new Entry([
            'sectionId' => $section->id,
            'typeId' => $entryType->id,
            'fieldLayoutId' => $entryType->fieldLayoutId,
            'authorId' => $user->id,
            'title' => 'My Entry',
            'slug' => 'my-entry',
            'postDate' => new DateTime(),
        ]);
        Craft::$app->elements->saveElement($entry);
        return $entry;
    }
}
