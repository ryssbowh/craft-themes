<?php

use Codeception\Test\Unit;
use Craft;
use Ryssbowh\CraftThemesTests\fixtures\ThemesFixture;
use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\services\LayoutService;
use UnitTester;
use craft\commerce\Plugin as Commerce;
use craft\commerce\models\ProductType;
use craft\commerce\models\ProductTypeSite;

class EcommerceTest extends Unit
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
    }

    /**
     * Base displays : 102
     * 
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
     */
    public function testCountDisplaysAreInstalled()
    {
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
