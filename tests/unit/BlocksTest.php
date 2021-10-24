<?php

use Codeception\Test\Unit;
use Craft;
use Ryssbowh\CraftThemesTests\fixtures\InstallThemeFixture;
use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\BlockProviderException;
use Ryssbowh\CraftThemes\exceptions\ThemeException;
use UnitTester;

class BlocksTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    protected $blocks;
    protected $layouts;

    protected function _before()
    {
        \Craft::$app->plugins->installPlugin('child-theme');
        $this->blocks = Themes::getInstance()->blocks;
        $this->layouts = Themes::getInstance()->layouts;
    }

    public function testCountRegions()
    {
        $layout = $this->layouts->getDefault('child-theme');
        $this->assertCount(8, $layout->regions);
        $this->assertTrue($layout->hasRegion('header-left'));
        $this->assertTrue($layout->hasRegion('header-right'));
        $this->assertTrue($layout->hasRegion('content'));
        $this->assertTrue($layout->hasRegion('banner'));
        $this->assertTrue($layout->hasRegion('before-content'));
        $this->assertTrue($layout->hasRegion('after-content'));
        $this->assertTrue($layout->hasRegion('footer-left'));
        $this->assertTrue($layout->hasRegion('footer-right'));
        $layout = $this->layouts->getDefault('parent-theme');
        $this->assertCount(8, $layout->regions);
        $this->assertTrue($layout->hasRegion('header-left'));
        $this->assertTrue($layout->hasRegion('header-right'));
        $this->assertTrue($layout->hasRegion('content'));
        $this->assertTrue($layout->hasRegion('banner'));
        $this->assertTrue($layout->hasRegion('before-content'));
        $this->assertTrue($layout->hasRegion('after-content'));
        $this->assertTrue($layout->hasRegion('footer-left'));
        $this->assertTrue($layout->hasRegion('footer-right'));
    }

    public function testCreatingBlocks()
    {
        $defaultLayout = $this->layouts->getDefault('child-theme');
        $block = $this->createBlock();
        $defaultLayout->addBlock($block, 'content');
        $this->layouts->save($defaultLayout);
        $this->assertCount(1, $this->blocks->all());
        $this->assertCount(1, $defaultLayout->getRegion('content')->blocks);
        $this->assertCount(1, $defaultLayout->getBlocks());
        $this->blocks->delete($block);
        $this->assertCount(0, $this->blocks->all());
        $this->assertCount(0, $defaultLayout->getRegion('content')->blocks);
        $this->assertCount(0, $defaultLayout->getBlocks());

        $block = $this->createBlock('content');
        $block->layout = $defaultLayout;
        $this->assertTrue($this->blocks->save($block));
        $this->assertCount(1, $this->blocks->all());
        $this->assertCount(1, $defaultLayout->getRegion('content')->blocks);
        $this->assertCount(1, $defaultLayout->getBlocks());
        $this->blocks->delete($block);
        $this->assertCount(0, $this->blocks->all());
        $this->assertCount(0, $defaultLayout->getRegion('content')->blocks);
        $this->assertCount(0, $defaultLayout->getBlocks());

        $_this = $this;
        $this->tester->expectThrowable(BlockProviderException::class, function () use ($_this) {
            $_this->blocks->create([
                'provider' => 'wrongProvider',
                'handle' => 'content'
            ]);
        });

        $this->tester->expectThrowable(BlockProviderException::class, function () use ($_this) {
            $_this->blocks->create([
                'provider' => 'system',
                'handle' => 'wrongHandle'
            ]);
        });

        $this->tester->expectThrowable(ThemeException::class, function () use ($_this, $defaultLayout) {
            $block = $_this->createBlock();
            $defaultLayout->addBlock($block, 'wrongRegion');
        });
    }

    protected function createBlock($region = null)
    {
        $block = $this->blocks->create([
            'provider' => 'system',
            'handle' => 'content',
            'region' => $region
        ]);
        return $block;
    }
}
