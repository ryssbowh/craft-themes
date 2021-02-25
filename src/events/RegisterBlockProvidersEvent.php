<?php 

namespace Ryssbowh\CraftThemes\events;

use Ryssbowh\CraftThemes\exceptions\BlockProviderException;
use Ryssbowh\CraftThemes\interfaces\BlockProviderInterface;
use yii\base\Event;

class RegisterBlockProvidersEvent extends Event
{
    /**
     * @var array
     */
    protected $providers = [];

    /**
     * Add a block provider
     * 
     * @param  BlockProviderInterface $provider
     * @return RegisterBlockProvidersEvent
     */
    public function add(BlockProviderInterface $provider)
    {
        $this->providers[$provider->handle] = $provider;
        return $this;
    }

    /**
     * Get all providers
     * 
     * @return array
     */
    public function getProviders(): array
    {
        return $this->providers;
    }
}