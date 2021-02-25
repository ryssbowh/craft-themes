<?php 

namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\events\RegisterBlockProvidersEvent;
use Ryssbowh\CraftThemes\exceptions\BlockProviderException;
use Ryssbowh\CraftThemes\interfaces\BlockProviderInterface;
use craft\base\Component;

class BlockProvidersService extends Component
{
    const REGISTER_BLOCK_PROVIDERS = 'block_providers';

    /**
     * @var array
     */
    protected $providers = [];

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        $e = new RegisterBlockProvidersEvent();
        $this->trigger(self::REGISTER_BLOCK_PROVIDERS, $e);
        $this->providers = $e->getProviders();
    }

    /**
     * Get all block providers
     * 
     * @param  bool $asArrays
     * @return array
     */
    public function getAll(bool $asArrays = false): array
    {
        if ($asArrays) {
            return array_map(function ($provider) {
                return $provider->toArray();
            }, $this->providers);
        }
        return $this->providers;
    }

    /**
     * Get provider by handle
     * 
     * @param  string $handle
     * @return BlockProviderInterface
     * @throws BlockProviderException
     */
    public function getByHandle(string $handle): BlockProviderInterface
    {
        if (isset($this->providers[$handle])) {
            return $this->providers[$handle];
        }
        throw BlockProviderException::notDefined($handle);
    }
}