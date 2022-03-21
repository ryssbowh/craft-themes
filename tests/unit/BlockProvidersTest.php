<?php

use Codeception\Test\Unit;
use Craft;
use Ryssbowh\CraftThemesTests\fixtures\ThemesFixture;
use Ryssbowh\CraftThemesTests\themes\child\models\blockProviders\TestBlockProvider;
use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\blockProviders\SystemBlockProvider;
use Ryssbowh\CraftThemes\events\RegisterBlockProviders;
use Ryssbowh\CraftThemes\exceptions\BlockProviderException;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\interfaces\BlockProviderInterface;
use Ryssbowh\CraftThemes\services\BlockProvidersService;
use UnitTester;
use yii\base\Event;

class BlockProvidersTest extends Unit
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
        $this->providers = Themes::getInstance()->blockProviders;
    }

    public function testExceptionIsThrownWhenSameProviderIsRegistered()
    {
        $providers = $this->providers;
        Event::on(BlockProvidersService::class, BlockProvidersService::EVENT_REGISTER_BLOCK_PROVIDERS, function (RegisterBlockProviders $event) {
            $event->add(new TestBlockProvider);
            $event->add(new TestBlockProvider);
        });
        $this->tester->expectThrowable(BlockProviderException::class, function () use ($providers) {
            $providers->all;
        });
    }

    public function testProvidersAreRegistered()
    {
        $providers = $this->providers;
        Event::on(BlockProvidersService::class, BlockProvidersService::EVENT_REGISTER_BLOCK_PROVIDERS, function (RegisterBlockProviders $event) {
            $event->add(new TestBlockProvider);
        });
        $this->tester->expectEvent(BlockProvidersService::class, BlockProvidersService::EVENT_REGISTER_BLOCK_PROVIDERS, function () use ($providers) {
            $providers->all;
        });
        $this->assertCount(3, $providers->all);
        $this->assertInstanceOf(BlockProviderInterface::class, $providers->getByHandle('system'));
        $this->assertInstanceOf(BlockProviderInterface::class, $providers->getByHandle('forms'));
        $this->assertInstanceOf(BlockProviderInterface::class, $providers->getByHandle('child'));
    }

    public function testTestBlockProviderHasBlocks()
    {
        $systemProvider = $this->providers->getByHandle('system');
        $this->tester->expectEvent(SystemBlockProvider::class, BlockProviderInterface::EVENT_REGISTER_BLOCKS, function () use ($systemProvider) {
            $systemProvider->getBlocks();
        });
        $this->assertCount(11, $systemProvider->getBlocks());
    }

    public function testBlockIsCreated()
    {
        $systemProvider = $this->providers->getByHandle('system');
        $block = $systemProvider->createBlock('content');
        $this->assertInstanceOf(BlockInterface::class, $block);
    }
}
