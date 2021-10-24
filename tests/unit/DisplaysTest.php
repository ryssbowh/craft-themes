<?php

use Codeception\Test\Unit;
use Craft;
use Ryssbowh\CraftThemesTests\fixtures\CategoryGroupsFixture;
use Ryssbowh\CraftThemesTests\fixtures\FieldsFixture;
use Ryssbowh\CraftThemesTests\fixtures\GlobalSetsFixture;
use Ryssbowh\CraftThemesTests\fixtures\InstallThemeFixture;
use Ryssbowh\CraftThemesTests\fixtures\SectionsFixture;
use Ryssbowh\CraftThemesTests\fixtures\TagGroupsFixture;
use Ryssbowh\CraftThemesTests\fixtures\VolumesFixture;
use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\DisplayInterface;
use Ryssbowh\CraftThemes\models\fieldDisplayers\AssetLink;
use Ryssbowh\CraftThemes\models\fieldDisplayers\AuthorDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\FileDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\TagTitleDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\TitleDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\UserInfoDefault;
use Ryssbowh\CraftThemes\models\fields\Author;
use Ryssbowh\CraftThemes\models\fields\File;
use Ryssbowh\CraftThemes\models\fields\TagTitle;
use Ryssbowh\CraftThemes\models\fields\Title;
use Ryssbowh\CraftThemes\models\fields\UserInfo;
use Ryssbowh\CraftThemes\services\FieldDisplayerService;
use Ryssbowh\CraftThemes\services\LayoutService;
use UnitTester;

class DisplaysTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    protected $displays;
    protected $fieldDisplayers;
    protected $fileDisplayers;
    protected $layouts;

    protected function _before()
    {
        \Craft::$app->plugins->installPlugin('child-theme');
        $this->displays = Themes::getInstance()->displays;
        $this->layouts = Themes::getInstance()->layouts;
        $this->fieldDisplayers = Themes::getInstance()->fieldDisplayers;
        $this->fileDisplayers = Themes::getInstance()->fileDisplayers;
    }

    /**
     * There is 19 fields per section, plus the title. 3 sections : 20 * 3 : 60
     * User user info field : 1
     * Channel & Structure author field : 2
     * Category title field : 1
     * Global asset field : 1
     * Tag layout title field : 1
     * Volume title and file fields : 2
     *
     * Total : 68
     *
     * 2 non-partial themes 68*2 = 136 displays in total
     *
     * There's 8 file displayers defined by the system
     * There's 33 field displayers defined by the system
     * 
     */
    public function testDisplaysAreInstalled()
    {
        $this->assertCount(8, $this->fileDisplayers->all());
        $this->assertCount(33, $this->fieldDisplayers->all());
        $this->assertCount(136, $this->displays->all());

        //User layouts
        $layout = $this->layouts->get('child-theme', LayoutService::USER_HANDLE);
        $displays = $layout->getViewMode('default')->displays;
        $this->assertCount(1, $displays);
        $this->assertInstanceOf(UserInfo::class, $displays[0]->item);
        $displayer = $displays[0]->item->displayer;
        $this->assertInstanceOf(UserInfoDefault::class, $displayer);
        $this->assertFalse($displayer->options->email);

        //category groups layouts
        $group = \Craft::$app->categories->getGroupByHandle('category');
        $layout = $this->layouts->get('child-theme', LayoutService::CATEGORY_HANDLE, $group->uid);
        $displays = $layout->getViewMode('default')->displays;
        $this->assertCount(1, $displays);
        $this->assertInstanceOf(Title::class, $displays[0]->item);
        $displayer = $displays[0]->item->displayer;
        $this->assertInstanceOf(TitleDefault::class, $displayer);
        $this->assertEquals('h1', $displayer->options->tag);

        //sections layouts
        $section = \Craft::$app->sections->getSectionByHandle('channel');
        $layout = $this->layouts->get('child-theme', LayoutService::ENTRY_HANDLE, $section->entryTypes[0]->uid);
        $displays = $layout->getViewMode('default')->displays;
        $this->assertCount(21, $displays);
        $this->assertInstanceOf(Title::class, $displays[0]->item);
        $this->assertInstanceOf(Author::class, $displays[1]->item);
        $this->assertInstanceOf(AuthorDefault::class, $displays[1]->item->displayer);

        $section = \Craft::$app->sections->getSectionByHandle('structure');
        $layout = $this->layouts->get('child-theme', LayoutService::ENTRY_HANDLE, $section->entryTypes[0]->uid);
        $displays = $layout->getViewMode('default')->displays;
        $this->assertCount(21, $displays);
        $this->assertInstanceOf(Title::class, $displays[0]->item);
        $this->assertInstanceOf(TitleDefault::class, $displays[0]->item->displayer);
        $this->assertInstanceOf(Author::class, $displays[1]->item);
        $this->assertInstanceOf(AuthorDefault::class, $displays[1]->item->displayer);

        $section = \Craft::$app->sections->getSectionByHandle('single');
        $layout = $this->layouts->get('child-theme', LayoutService::ENTRY_HANDLE, $section->entryTypes[0]->uid);
        $displays = $layout->getViewMode('default')->displays;
        $this->assertCount(20, $displays);
        $this->assertInstanceOf(Title::class, $displays[0]->item);
        $this->assertInstanceOf(TitleDefault::class, $displays[0]->item->displayer);

        //globals layouts
        $global = \Craft::$app->globals->getSetByHandle('global');
        $layout = $this->layouts->get('child-theme', LayoutService::GLOBAL_HANDLE, $global->uid);
        $displays = $layout->getViewMode('default')->displays;
        $this->assertCount(1, $displays);
        $this->assertInstanceOf(AssetLink::class, $displays[0]->item->displayer);

        //Tags layouts
        $group = \Craft::$app->tags->getTagGroupByHandle('tag');
        $layout = $this->layouts->get('child-theme', LayoutService::TAG_HANDLE, $group->uid);
        $displays = $layout->getViewMode('default')->displays;
        $this->assertCount(1, $displays);
        $this->assertInstanceOf(TagTitle::class, $displays[0]->item);
        $this->assertInstanceOf(TagTitleDefault::class, $displays[0]->item->displayer);

        //volumes layouts
        $volume = \Craft::$app->volumes->getVolumeByHandle('public');
        $layout = $this->layouts->get('child-theme', LayoutService::VOLUME_HANDLE, $volume->uid);
        $displays = $layout->getViewMode('default')->displays;
        $this->assertCount(2, $displays);
        $this->assertInstanceOf(Title::class, $displays[0]->item);
        $this->assertInstanceOf(File::class, $displays[1]->item);
        $this->assertInstanceOf(FileDefault::class, $displays[1]->item->displayer);

    }

    public function testAddingFieldToEntryType()
    {
        // $sectionsFixture = $this->tester->grabFixture('sections');
        // $fieldsFixture = $this->tester->grabFixture('fields');
        // $section = $sectionsFixture->getSection(0);
        // $entryType = $section->entryTypes[0];

        // $assetField = $fieldsFixture->getField('assets');
        // // $this->displays->tests = true;
        // // $this->layouts->tests = true;
        // $sectionsFixture->addFieldToEntryType($assetField, $entryType);
        // dump($entryType->fieldLayout);
        // dd(\Craft::$app->sections->getEntryTypeById($entryType->id)->fieldLayout);
        // $layout = $this->layouts->get('child-theme', LayoutService::ENTRY_HANDLE, $type->uid);
        // $this->assertCount(3, $this->displays->getForLayout($layout));
    }
}
