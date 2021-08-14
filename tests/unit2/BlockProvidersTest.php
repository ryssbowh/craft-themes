<?php

use Codeception\Test\Unit;
use Craft;
use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\events\RegisterBlockProviders;
use Ryssbowh\CraftThemes\exceptions\BlockProviderException;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\interfaces\BlockProviderInterface;
use Ryssbowh\CraftThemes\models\SystemBlockProvider;
use Ryssbowh\CraftThemes\services\BlockProvidersService;
use Ryssbowh\tests\themes\child\models\blockProviders\BootstrapBlockProvider;
use UnitTester;
use yii\base\Event;

class BlockProvidersTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    protected function _before()
    {
        $this->providers = Themes::getInstance()->blockProviders;
    }

    public function testExceptionIsThrownWhenSameProviderIsRegistered()
    {
        $providers = $this->providers;
        Event::on(BlockProvidersService::class, BlockProvidersService::REGISTER_BLOCK_PROVIDERS, function (RegisterBlockProviders $event) {
            $event->add(new BootstrapBlockProvider);
            $event->add(new BootstrapBlockProvider);
        });
        $this->tester->expectThrowable(BlockProviderException::class, function () use ($providers) {
            $providers->all();
        });
    }

    public function testProvidersAreRegistered()
    {
        $providers = $this->providers;
        Event::on(BlockProvidersService::class, BlockProvidersService::REGISTER_BLOCK_PROVIDERS, function (RegisterBlockProviders $event) {
            $event->add(new BootstrapBlockProvider);
        });
        $this->tester->expectEvent(BlockProvidersService::class, BlockProvidersService::REGISTER_BLOCK_PROVIDERS, function () use ($providers) {
            $providers->all();
        });
        $this->assertCount(2, $providers->all());
        $this->assertInstanceOf(BlockProviderInterface::class, $providers->getByHandle('system'));
        $this->assertInstanceOf(BlockProviderInterface::class, $providers->getByHandle('child'));
    }

    public function testSystemBlockProviderHasBlocks()
    {
        $systemProvider = $this->providers->getByHandle('system');
        $this->tester->expectEvent(SystemBlockProvider::class, BlockProviderInterface::REGISTER_BLOCKS, function () use ($systemProvider) {
            $systemProvider->getBlocks();
        });
        $this->assertCount(10, $systemProvider->getBlocks());
    }

    public function testBlockIsCreated()
    {
        $systemProvider = $this->providers->getByHandle('system');
        $block = $systemProvider->createBlock('content');
        $this->assertInstanceOf(BlockInterface::class, $block);
    }
}
