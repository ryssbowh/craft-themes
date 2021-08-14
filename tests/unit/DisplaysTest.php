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

    public function _fixtures()
    {
        return [
            'themes' => InstallThemeFixture::class,
            'groups' => CategoryGroupsFixture::class,
            'sections' => SectionsFixture::class,
            'globals' => GlobalSetsFixture::class,
            'volumes' => VolumesFixture::class,
            'tags' => TagGroupsFixture::class,
            'fields' => FieldsFixture::class
        ];
    }

    protected function _before()
    {
        $this->displays = Themes::getInstance()->displays;
        $this->layouts = Themes::getInstance()->layouts;
        $this->fieldDisplayers = Themes::getInstance()->fieldDisplayers;
        $this->fileDisplayers = Themes::getInstance()->fileDisplayers;
    }

    public function testFieldDisplayers()
    {
        $_this = $this;
        $this->tester->expectEvent(FieldDisplayerService::class, FieldDisplayerService::REGISTER_DISPLAYERS, function () use ($_this) {
            $_this->fieldDisplayers->all();
        });
        $this->assertCount(30, $this->fieldDisplayers->all());
    }

    public function testFileDisplayers()
    {
        $this->assertCount(8, $this->fileDisplayers->all());
    }

    public function testDisplays()
    {
        $this->assertCount(26, $this->displays->all());

        $layout = $this->layouts->get('child-theme', LayoutService::USER_HANDLE);
        $this->assertCount(1, $layout->displays);
        $this->assertInstanceOf(UserInfo::class, $layout->displays[0]->item);
        $displayer = $layout->displays[0]->item->displayer;
        $this->assertInstanceOf(UserInfoDefault::class, $displayer);
        $this->assertFalse($displayer->options->email);

        $fixture = $this->tester->grabFixture('groups');
        $group = $fixture->getGroup(0);
        $layout = $this->layouts->get('child-theme', LayoutService::CATEGORY_HANDLE, $group->uid);
        $this->assertCount(1, $layout->displays);
        $group = $fixture->getGroup(1);
        $layout = $this->layouts->get('child-theme', LayoutService::CATEGORY_HANDLE, $group->uid);
        $this->assertCount(1, $layout->displays);
        $this->assertInstanceOf(Title::class, $layout->displays[0]->item);
        $displayer = $layout->displays[0]->item->displayer;
        $this->assertInstanceOf(TitleDefault::class, $displayer);
        $this->assertEquals('h1', $displayer->options->tag);

        $fixture = $this->tester->grabFixture('sections');
        $section = $fixture->getSection(0);
        $layout = $this->layouts->get('child-theme', LayoutService::ENTRY_HANDLE, $section->entryTypes[0]->uid);
        $this->assertCount(2, $layout->displays);
        $section = $fixture->getSection(1);
        $layout = $this->layouts->get('child-theme', LayoutService::ENTRY_HANDLE, $section->entryTypes[0]->uid);
        $this->assertCount(2, $layout->displays);
        $this->assertInstanceOf(Title::class, $layout->displays[0]->item);
        $this->assertInstanceOf(Author::class, $layout->displays[1]->item);
        $this->assertInstanceOf(AuthorDefault::class, $layout->displays[1]->item->displayer);

        $fixture = $this->tester->grabFixture('globals');
        $global = $fixture->getSet(0);
        $layout = $this->layouts->get('child-theme', LayoutService::GLOBAL_HANDLE, $global->uid);
        $this->assertCount(0, $layout->displays);
        $global = $fixture->getSet(1);
        $layout = $this->layouts->get('child-theme', LayoutService::GLOBAL_HANDLE, $global->uid);
        $this->assertCount(0, $layout->displays);

        $fixture = $this->tester->grabFixture('tags');
        $group = $fixture->getGroup(0);
        $layout = $this->layouts->get('child-theme', LayoutService::TAG_HANDLE, $group->uid);
        $this->assertCount(1, $layout->displays);
        $group = $fixture->getGroup(1);
        $layout = $this->layouts->get('child-theme', LayoutService::TAG_HANDLE, $group->uid);
        $this->assertCount(1, $layout->displays);
        $this->assertInstanceOf(TagTitle::class, $layout->displays[0]->item);
        $this->assertInstanceOf(TagTitleDefault::class, $layout->displays[0]->item->displayer);

        $fixture = $this->tester->grabFixture('volumes');
        $volume = $fixture->getVolume(0);
        $layout = $this->layouts->get('child-theme', LayoutService::VOLUME_HANDLE, $volume->uid);
        $this->assertCount(2, $layout->displays);
        $volume = $fixture->getVolume(1);
        $layout = $this->layouts->get('child-theme', LayoutService::VOLUME_HANDLE, $volume->uid);
        $this->assertCount(2, $layout->displays);
        $this->assertInstanceOf(Title::class, $layout->displays[0]->item);
        $this->assertInstanceOf(File::class, $layout->displays[1]->item);
        $displayer = $layout->displays[1]->item->displayer;
        $this->assertInstanceOf(FileDefault::class, $displayer);

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
