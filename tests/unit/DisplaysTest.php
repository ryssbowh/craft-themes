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
        $this->viewModes = Themes::getInstance()->viewModes;
        $this->fieldDisplayers = Themes::getInstance()->fieldDisplayers;
        $this->fileDisplayers = Themes::getInstance()->fileDisplayers;
    }

    /**
     * There are 3 sections : Channel, Structure, Single.
     *
     * Single : 24
     *     - 19 fields
     *     - date updated/created/posted
     *     - url
     *     - title
     * Channel : 25
     *     - 19 fields
     *     - date updated/created/posted
     *     - author
     *     - url
     *     - title
     * Structure : 25
     *     - 19 fields
     *     - date updated/created/posted
     *     - author
     *     - url
     *     - title
     * User : 8
     *     - username
     *     - first name
     *     - last name
     *     - photo
     *     - email
     *     - date updated/created/last login
     * Global : 3
     *     - date updated/created
     *     - asset field
     * Category : 4
     *     - title
     *     - date updated/created
     *     - url
     * Tag : 3
     *     - date updated/created
     *     - title
     * Volume : 4
     *     - title
     *     - file
     *     - date updated/created
     *
     * Total : 96
     *
     * 2 non-partial themes 96*2 = 192 displays in total
     *
     * There's 8 file displayers defined by the system
     * There's 39 field displayers defined by the system
     * 
     */
    public function testDisplaysAreInstalled()
    {
        $this->assertCount(8, $this->fileDisplayers->all());
        $this->assertCount(39, $this->fieldDisplayers->all());
        $this->assertCount(192, $this->displays->all());

        //User layouts
        $layout = $this->layouts->get('child-theme', LayoutService::USER_HANDLE);
        $displays = $layout->getViewMode('default')->displays;
        $this->assertCount(8, $displays);

        //category groups layouts
        $group = \Craft::$app->categories->getGroupByHandle('category');
        $layout = $this->layouts->get('child-theme', LayoutService::CATEGORY_HANDLE, $group->uid);
        $displays = $layout->getViewMode('default')->displays;
        $this->assertCount(4, $displays);

        //sections layouts
        $section = \Craft::$app->sections->getSectionByHandle('channel');
        $layout = $this->layouts->get('child-theme', LayoutService::ENTRY_HANDLE, $section->entryTypes[0]->uid);
        $displays = $layout->getViewMode('default')->displays;
        $this->assertCount(25, $displays);

        $section = \Craft::$app->sections->getSectionByHandle('structure');
        $layout = $this->layouts->get('child-theme', LayoutService::ENTRY_HANDLE, $section->entryTypes[0]->uid);
        $displays = $layout->getViewMode('default')->displays;
        $this->assertCount(25, $displays);

        $section = \Craft::$app->sections->getSectionByHandle('single');
        $layout = $this->layouts->get('child-theme', LayoutService::ENTRY_HANDLE, $section->entryTypes[0]->uid);
        $displays = $layout->getViewMode('default')->displays;
        $this->assertCount(24, $displays);

        //globals layouts
        $global = \Craft::$app->globals->getSetByHandle('global');
        $layout = $this->layouts->get('child-theme', LayoutService::GLOBAL_HANDLE, $global->uid);
        $displays = $layout->getViewMode('default')->displays;
        $this->assertCount(3, $displays);

        //Tags layouts
        $group = \Craft::$app->tags->getTagGroupByHandle('tag');
        $layout = $this->layouts->get('child-theme', LayoutService::TAG_HANDLE, $group->uid);
        $displays = $layout->getViewMode('default')->displays;
        $this->assertCount(3, $displays);

        //volumes layouts
        $volume = \Craft::$app->volumes->getVolumeByHandle('public');
        $layout = $this->layouts->get('child-theme', LayoutService::VOLUME_HANDLE, $volume->uid);
        $displays = $layout->getViewMode('default')->displays;
        $this->assertCount(4, $displays);

    }

    // public function testAddingFieldToEntryType()
    // {
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
    // }
}
