<?php

use Codeception\Test\Unit;
use Craft;
use Ryssbowh\CraftThemesTests\fixtures\EntriesFixture;
use Ryssbowh\CraftThemesTests\fixtures\InstallThemeFixture;
use Ryssbowh\CraftThemesTests\fixtures\SectionsFixture;
use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\services\DisplayService;
use Ryssbowh\CraftThemes\services\LayoutService;
use UnitTester;
use craft\elements\Entry;
use yii\base\Event;

class ViewTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    public function _fixtures()
    {
        return [
            'themes' => InstallThemeFixture::class,
            'sections' => SectionsFixture::class
        ];
    }

    protected function _before()
    {
        $this->layouts = Themes::getInstance()->layouts;
        $this->view = Themes::getInstance()->view;
        $this->rules = Themes::getInstance()->rules;
        //Setting theme for console requests and re-resolve current theme
        $this->rules->console = 'child-theme';
        $this->rules->setConsole = true;
        $this->rules->resolveCurrentTheme();
        \Craft::$app->view->setTemplateMode('site');
    }

    public function testRendering()
    {
        $fixture = $this->tester->grabFixture('sections');
        $section = $fixture->getSection(0);
        $entryTypes = $section->getEntryTypes();
        $entryType = reset($entryTypes);
        $entry = $this->createEntry($section, $entryType);

        $layout = $this->layouts->get('child-theme', 'entry', $entryType->uid);
        $html = $this->view->renderLayout($layout, 'default', $entry);
        $this->assertStringContainsString('<div class="layout layout-entry view-mode-default handle-default">', $html);
        $this->assertStringContainsString("<h1>
                    My Entry
            </h1>", $html);
        $this->assertStringContainsString('<div class="display field field-title title_default">', $html);
        $this->assertStringContainsString('<div class="display field field-author author_default">', $html);

    }

    protected function createEntry($section, $entryType)
    {
        $entry = new Entry([
            'sectionId' => $section->id,
            'typeId' => $entryType->id,
            'fieldLayoutId' => $entryType->fieldLayoutId,
            'authorId' => 1,
            'title' => 'My Entry',
            'slug' => 'my-entry',
            'postDate' => new DateTime(),
        ]);
        Craft::$app->elements->saveElement($entry);
        return $entry;
    }
}
