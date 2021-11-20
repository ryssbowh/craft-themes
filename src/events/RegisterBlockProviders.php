<?php
namespace Ryssbowh\CraftThemes\events;

use Ryssbowh\CraftThemes\blockProviders\FormsBlockProvider;
use Ryssbowh\CraftThemes\blockProviders\SystemBlockProvider;
use Ryssbowh\CraftThemes\exceptions\BlockProviderException;
use Ryssbowh\CraftThemes\interfaces\BlockProviderInterface;
use yii\base\Event;

class RegisterBlockProviders extends Event
{
    /**
     * @var array
     */
    protected $providers = [];

    public function init()
    {
        parent::init();
        $this->add(new SystemBlockProvider);
        $this->add(new FormsBlockProvider);
    }

    /**
     * Add a block provider
     * 
     * @param  BlockProviderInterface $provider
     * @return RegisterBlockProviders
     * @throws BlockProviderException
     */
    public function add(BlockProviderInterface $provider)
    {
        if (isset($this->providers[$provider->handle])) {
            throw BlockProviderException::defined($provider->handle);
        }
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