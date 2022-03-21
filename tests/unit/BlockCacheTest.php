<?php

use Codeception\Test\Unit;
use Craft;
use Ryssbowh\CraftThemesTests\fixtures\ThemesFixture;
use Ryssbowh\CraftThemesTests\themes\child\models\cacheStrategies\TestBlockCacheStrategy;
use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\events\RegisterBlockCacheStrategies;
use Ryssbowh\CraftThemes\interfaces\BlockCacheStrategyInterface;
use Ryssbowh\CraftThemes\services\BlockCacheService;
use UnitTester;
use yii\base\Event;

class BlockCacheTest extends Unit
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
        $this->blockCache = Themes::getInstance()->blockCache;
        $this->blockCache->cacheEnabled = true;
        $this->layouts = Themes::getInstance()->layouts;
        $this->blocks = Themes::getInstance()->blocks;
    }

    public function testStrategiesAreRegistered()
    {
        $blockCache = $this->blockCache;
        Event::on(BlockCacheService::class, BlockCacheService::REGISTER_STRATEGIES, function (RegisterBlockCacheStrategies $event) {
            $event->add(new TestBlockCacheStrategy);
        });
        $this->tester->expectEvent(BlockCacheService::class, BlockCacheService::REGISTER_STRATEGIES, function () use ($blockCache) {
            $blockCache->strategies;
        });
        $this->assertCount(4, $blockCache->strategies);
        $this->assertInstanceOf(BlockCacheStrategyInterface::class, $blockCache->getStrategy('test'));
        $this->assertInstanceOf(BlockCacheStrategyInterface::class, $blockCache->getStrategy('global'));
        $this->assertInstanceOf(BlockCacheStrategyInterface::class, $blockCache->getStrategy('query'));
        $this->assertInstanceOf(BlockCacheStrategyInterface::class, $blockCache->getStrategy('path'));
    }

    public function testBlockIsCached()
    {
        $defaultLayout = $this->layouts->getDefault('child-theme');
        $block = $this->blocks->create([
            'provider' => 'system',
            'handle' => 'sitename',
            'cacheStrategy' => [
                'handle' => 'global'
            ]
        ]);
        $defaultLayout->addBlock($block, 'content');
        $this->blockCache->startCaching($block);
        $this->blockCache->stopCaching($block, 'this is the site name block');
        $this->assertEquals('this is the site name block', $this->blockCache->getCache($block));
    }
}
