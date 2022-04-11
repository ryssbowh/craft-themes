<?php

use Codeception\Test\Unit;
use Craft;
use Ryssbowh\CraftThemesTests\fixtures\ThemesFixture;
use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\services\LayoutService;
use UnitTester;
use craft\commerce\Plugin as Commerce;

class DisplaysTest extends Unit
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
        $this->displays = Themes::getInstance()->displays;
        $this->layouts = Themes::getInstance()->layouts;
        $this->viewModes = Themes::getInstance()->viewModes;
        $this->fieldDisplayers = Themes::getInstance()->fieldDisplayers;
        $this->fileDisplayers = Themes::getInstance()->fileDisplayers;
    }

    /**
     * Single : 26
     *     - 21 fields
     *     - date updated/created/posted
     *     - url
     *     - title
     * Channel : 28
     *     - 21 fields
     *     - date updated/created/posted/expiry
     *     - author
     *     - url
     *     - title
     * Structure : 28
     *     - 21 fields
     *     - date updated/created/posted/expiry
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
     * Global : 4
     *     - date updated/created
     *     - asset field
     *     - category field
     * Category : 4
     *     - title
     *     - date updated/created
     *     - url
     * Tag : 3
     *     - date updated/created
     *     - title
     * Volume : 5
     *     - title
     *     - file
     *     - date updated/created
     *     - url
     * Product : 7
     *     - date posted/updated/created/expiry
     *     - title
     *     - url
     *     - variants
     * Variant: 9
     *     - title
     *     - date updated/created
     *     - stock
     *     - sku
     *     - price
     *     - dimensions
     *     - weight
     *     - allowed quantity
     *
     * Total : 122
     *
     * 2 non-partial themes 122*2 = 244 displays in total
     *
     * There's 8 file displayers defined by the system
     * There's 48 field displayers defined by the system
     * 
     */
    public function testDisplaysAreInstalled()
    {
        $this->assertCount(8, $this->fileDisplayers->all);
        $this->assertCount(48, $this->fieldDisplayers->all);
        $this->assertCount(244, $this->displays->all);

        //User layouts
        $layout = $this->layouts->get('child-theme', 'user');
        $displays = $layout->getViewMode('default')->displays;
        $this->assertCount(8, $displays);

        //category groups layouts
        $group = \Craft::$app->categories->getGroupByHandle('category');
        $layout = $this->layouts->get('child-theme', 'category', $group->uid);
        $displays = $layout->getViewMode('default')->displays;
        $this->assertCount(4, $displays);

        //sections layouts
        $section = \Craft::$app->sections->getSectionByHandle('channel');
        $layout = $this->layouts->get('child-theme', 'entry', $section->entryTypes[0]->uid);
        $displays = $layout->getViewMode('default')->displays;
        $this->assertCount(28, $displays);

        $section = \Craft::$app->sections->getSectionByHandle('structure');
        $layout = $this->layouts->get('child-theme', 'entry', $section->entryTypes[0]->uid);
        $displays = $layout->getViewMode('default')->displays;
        $this->assertCount(28, $displays);

        $section = \Craft::$app->sections->getSectionByHandle('single');
        $layout = $this->layouts->get('child-theme', 'entry', $section->entryTypes[0]->uid);
        $displays = $layout->getViewMode('default')->displays;
        $this->assertCount(26, $displays);

        //globals layouts
        $global = \Craft::$app->globals->getSetByHandle('global');
        $layout = $this->layouts->get('child-theme', 'global', $global->uid);
        $displays = $layout->getViewMode('default')->displays;
        $this->assertCount(4, $displays);

        //Tags layouts
        $group = \Craft::$app->tags->getTagGroupByHandle('tag');
        $layout = $this->layouts->get('child-theme', 'tag', $group->uid);
        $displays = $layout->getViewMode('default')->displays;
        $this->assertCount(3, $displays);

        //volumes layouts
        $volume = \Craft::$app->volumes->getVolumeByHandle('public');
        $layout = $this->layouts->get('child-theme', 'volume', $volume->uid);
        $displays = $layout->getViewMode('default')->displays;
        $this->assertCount(5, $displays);

        //products layouts
        $type = Commerce::getInstance()->productTypes->getProductTypeByHandle('clothing');
        $layout = $this->layouts->get('child-theme', 'product', $type->uid);
        $displays = $layout->getViewMode('default')->displays;
        $this->assertCount(7, $displays);

        //variants layouts
        $layout = $this->layouts->get('child-theme', 'variant', $type->uid);
        $displays = $layout->getViewMode('default')->displays;
        $this->assertCount(9, $displays);
    }
}
