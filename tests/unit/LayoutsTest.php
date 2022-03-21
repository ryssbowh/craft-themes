<?php

use Codeception\Test\Unit;
use Craft;
use Ryssbowh\CraftThemesTests\fixtures\ThemesFixture;
use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\LayoutException;
use Ryssbowh\CraftThemes\exceptions\ThemeException;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\layouts\CategoryLayout;
use Ryssbowh\CraftThemes\models\layouts\CustomLayout;
use Ryssbowh\CraftThemes\models\layouts\EntryLayout;
use Ryssbowh\CraftThemes\models\layouts\GlobalLayout;
use Ryssbowh\CraftThemes\models\layouts\ProductLayout;
use Ryssbowh\CraftThemes\models\layouts\TagLayout;
use Ryssbowh\CraftThemes\models\layouts\VariantLayout;
use Ryssbowh\CraftThemes\models\layouts\VolumeLayout;
use Ryssbowh\CraftThemes\services\LayoutService;
use UnitTester;
use craft\commerce\Plugin as Commerce;
use craft\commerce\models\ProductType;
use craft\commerce\models\ProductTypeSite;
use craft\db\Table;

class LayoutsTest extends Unit
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
        $this->registry = Themes::getInstance()->registry;
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
     * 1 layout without displays (default) per non-partial theme
     */
    public function testLayoutCounts()
    {
        $this->assertCount(22, $this->layouts->all);
        $this->assertCount(11, $this->layouts->getForTheme('child-theme'));
        $this->assertCount(11, $this->layouts->getForTheme('parent-theme'));
        $this->assertCount(20, $this->layouts->withDisplays());
        $this->assertCount(0, $this->layouts->getForTheme('partial-theme'));
    }

    public function testCustomLayouts()
    {
        $layout = $this->layouts->createCustom([
            'name' => 'my layout',
            'elementUid' => 'my-layout',
            'themeHandle' => 'child-theme'
        ]);
        $layout2 = $this->layouts->createCustom([
            'name' => 'my layout',
            'elementUid' => 'my-layout',
            'themeHandle' => 'parent-theme'
        ]);
        $layout3 = $this->layouts->createCustom([
            'name' => 'my layout',
            'elementUid' => 'my-layout',
            'themeHandle' => 'child-theme'
        ]);
        $this->assertInstanceOf(CustomLayout::class, $layout);
        $this->assertTrue($this->layouts->save($layout));
        $this->assertCount(23, $this->layouts->all);
        $this->assertInstanceOf(CustomLayout::class, $this->layouts->getCustom('child-theme', 'my-layout'));

        $this->assertTrue($this->layouts->save($layout2));
        $this->assertCount(24, $this->layouts->all);
        $this->assertInstanceOf(CustomLayout::class, $this->layouts->getCustom('parent-theme', 'my-layout'));

        //This should get a validation error, the handle already exists :
        $this->assertFalse($this->layouts->save($layout3));

        $this->assertTrue($this->layouts->deleteCustom($layout));
        $this->assertCount(23, $this->layouts->all);

        $this->assertTrue($this->layouts->deleteCustom($layout2));
        $this->assertCount(22, $this->layouts->all);

        $this->assertNull($this->layouts->getCustom('child-theme', 'my-layout'));
        $this->assertNull($this->layouts->getCustom('parent-theme', 'my-layout'));
    }

    public function testUninstallTheme()
    {
        Craft::$app->plugins->uninstallPlugin('child-theme');
        $this->assertCount(11, $this->layouts->all);
        $this->assertCount(0, $this->layouts->getForTheme('child-theme'));
        $this->assertCount(11, $this->layouts->getForTheme('parent-theme'));
    }

    public function testUninstallPartialTheme()
    {
        Craft::$app->plugins->uninstallPlugin('partial-theme');
        $this->assertCount(0, $this->layouts->all);
    }

    public function testReinstall()
    {
        $this->layouts->installAll();
        $this->testLayoutCounts();
    }

    public function testElementsLayoutAreCreatedAndDeleted()
    {
        //Category groups layouts
        $catGroup = \Craft::$app->categories->getGroupByHandle('category');
        $layout = $this->layouts->get('child-theme', 'category', $catGroup->uid);
        $this->assertInstanceOf(CategoryLayout::class, $layout);
        \Craft::$app->categories->deleteGroup($catGroup);
        $layout = $this->layouts->get('child-theme', 'category', $catGroup->uid);
        $this->assertNull($layout);
        $this->assertCount(10, $this->layouts->getForTheme('child-theme'));

        //entry types layouts
        $channel = \Craft::$app->sections->getSectionByHandle('channel');
        $layout = $this->layouts->get('child-theme', 'entry', $channel->entryTypes[0]->uid);
        $this->assertInstanceOf(EntryLayout::class, $layout);
        \Craft::$app->sections->deleteSection($channel);
        $layout = $this->layouts->get('child-theme', 'entry', $channel->entryTypes[0]->uid);
        $this->assertNull($layout);
        $this->assertCount(9, $this->layouts->getForTheme('child-theme'));

        $structure = \Craft::$app->sections->getSectionByHandle('structure');
        $layout = $this->layouts->get('child-theme', 'entry', $structure->entryTypes[0]->uid);
        $this->assertInstanceOf(EntryLayout::class, $layout);
        \Craft::$app->sections->deleteSection($structure);
        $layout = $this->layouts->get('child-theme', 'entry', $structure->entryTypes[0]->uid);
        $this->assertNull($layout);
        $this->assertCount(8, $this->layouts->getForTheme('child-theme'));

        $single = \Craft::$app->sections->getSectionByHandle('single');
        $layout = $this->layouts->get('child-theme', 'entry', $single->entryTypes[0]->uid);
        $this->assertInstanceOf(EntryLayout::class, $layout);
        \Craft::$app->sections->deleteSection($single);
        $layout = $this->layouts->get('child-theme', 'entry', $single->entryTypes[0]->uid);
        $this->assertNull($layout);
        $this->assertCount(7, $this->layouts->getForTheme('child-theme'));

        //globals layouts
        $global = \Craft::$app->globals->getSetByHandle('global');
        $layout = $this->layouts->get('child-theme', 'global', $global->uid);
        $this->assertInstanceOf(GlobalLayout::class, $layout);
        \Craft::$app->globals->deleteGlobalSetById($global->id);
        $layout = $this->layouts->get('child-theme', 'global', $global->uid);
        $this->assertNull($layout);
        $this->assertCount(6, $this->layouts->getForTheme('child-theme'));

        //volumes layouts
        $volume = \Craft::$app->volumes->getVolumeByHandle('public');
        $layout = $this->layouts->get('child-theme', 'volume', $volume->uid);
        $this->assertInstanceOf(VolumeLayout::class, $layout);
        \Craft::$app->volumes->deleteVolume($volume);
        $layout = $this->layouts->get('child-theme', 'volume', $volume->uid);
        $this->assertNull($layout);
        $this->assertCount(5, $this->layouts->getForTheme('child-theme'));

        //Tags layouts
        $tagGroup = \Craft::$app->tags->getTagGroupByHandle('tag');
        $layout = $this->layouts->get('child-theme', 'tag', $tagGroup->uid);
        $this->assertInstanceOf(TagLayout::class, $layout);
        \Craft::$app->tags->deleteTagGroup($tagGroup);
        $layout = $this->layouts->get('child-theme', 'tag', $tagGroup->uid);
        $this->assertNull($layout);
        $this->assertCount(4, $this->layouts->getForTheme('child-theme'));

        //Product and variant
        $type = Commerce::getInstance()->productTypes->getProductTypeByHandle('clothing');
        $layout = $this->layouts->get('child-theme', 'product', $type->uid);
        $layout2 = $this->layouts->get('child-theme', 'variant', $type->uid);
        $this->assertInstanceOf(ProductLayout::class, $layout);
        $this->assertInstanceOf(VariantLayout::class, $layout2);
        Commerce::getInstance()->productTypes->deleteProductTypeById($type->id);
        $layout = $this->layouts->get('child-theme', 'product', $type->uid);
        $layout2 = $this->layouts->get('child-theme', 'variant', $type->uid);
        $this->assertNull($layout);
        $this->assertNull($layout2);
        $this->assertCount(2, $this->layouts->getForTheme('child-theme'));

        //Reinstating soft deleted elements, for some reason they will not be reinstated by the project config reset
        //See https://github.com/craftcms/cms/issues/10001
        \Craft::$app->getDb()->createCommand()
            ->update(Table::CATEGORYGROUPS, ['dateDeleted' => null], ['uid' => $catGroup->uid])
            ->execute();
        \Craft::$app->getDb()->createCommand()
            ->update(Table::SECTIONS, ['dateDeleted' => null], ['uid' => $channel->uid])
            ->execute();
        \Craft::$app->getDb()->createCommand()
            ->update(Table::SECTIONS, ['dateDeleted' => null], ['uid' => $structure->uid])
            ->execute();
        \Craft::$app->getDb()->createCommand()
            ->update(Table::SECTIONS, ['dateDeleted' => null], ['uid' => $single->uid])
            ->execute();
        \Craft::$app->getDb()->createCommand()
            ->update(Table::VOLUMES, ['dateDeleted' => null], ['uid' => $volume->uid])
            ->execute();
        \Craft::$app->getDb()->createCommand()
            ->update(Table::TAGGROUPS, ['dateDeleted' => null], ['uid' => $tagGroup->uid])
            ->execute();
        \Craft::$app->getDb()->createCommand()
            ->update(Table::FIELDLAYOUTS, ['dateDeleted' => null])
            ->execute();
        \Craft::$app->getDb()->createCommand()
            ->update(Table::ENTRYTYPES, ['dateDeleted' => null])
            ->execute();
        \Craft::$app->getDb()->createCommand()
            ->update(Table::ELEMENTS, ['dateDeleted' => null])
            ->execute();

        $productType = new ProductType([
            'name' => 'Clothing',
            'handle' => 'clothing',
            'hasDimensions' => true,
            'hasVariants' => true,
        ]);
        $allSiteSettings = [];
        foreach (Craft::$app->getSites()->getAllSites() as $site) {
            $siteSettings = new ProductTypeSite();
            $siteSettings->siteId = $site->id;
            $siteSettings->hasUrls = false;
            $siteSettings->uriFormat = null;
            $siteSettings->template = null;
            $allSiteSettings[$site->id] = $siteSettings;
        }
        $productType->setSiteSettings($allSiteSettings);
        Commerce::getInstance()->productTypes->saveProductType($productType);
    }
}
