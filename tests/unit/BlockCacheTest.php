<?php

use Codeception\Test\Unit;
use Craft;
use Ryssbowh\CraftThemesTests\fixtures\InstallThemeFixture;
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

    protected function _before()
    {
        \Craft::$app->plugins->installPlugin('child-theme');
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
    }

    public function testBlockIsCached()
    {
        $defaultLayout = $this->layouts->getDefault('child-theme');
        $block = $this->blocks->create([
            'provider' => 'system',
            'handle' => 'content',
            'options' => ['cacheStrategy' => 'global']
        ]);
        $strategy = $this->blockCache->getStrategy('global');
        $defaultLayout->addBlock($block, 'content');
        $this->blockCache->startBlockCaching($block);
        $this->blockCache->stopBlockCaching($block, 'this is the content block');
        $this->assertEquals('this is the content block', $this->blockCache->getBlockCache($block));
        $this->assertEquals('this is the content block', $strategy->getCache($block));
    }
}
