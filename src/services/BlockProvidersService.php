<?php 

namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\events\RegisterBlockProvidersEvent;
use Ryssbowh\CraftThemes\exceptions\BlockProviderException;
use Ryssbowh\CraftThemes\interfaces\BlockProviderInterface;
use craft\base\Component;

class BlockProvidersService extends Component
{
    /**
     * Register block providers event key
     * @var string
     */
    const REGISTER_BLOCK_PROVIDERS = 'block_providers';

    /**
     * @var array
     */
    protected $_providers;

    /**
     * Get all block providers
     * 
     * @param  bool $asArrays
     * @return array
     */
    public function all(bool $asArrays = false): array
    {
        if ($this->_providers === null) {
            $this->register();
        }
        if ($asArrays) {
            return array_map(function ($provider) {
                return $provider->toArray();
            }, $this->_providers);
        }
        return $this->_providers;
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
        if (isset($this->all()[$handle])) {
            return $this->all()[$handle];
        }
        throw BlockProviderException::notDefined($handle);
    }

    /**
     * Registers block providers
     */
    protected function register()
    {
        $e = new RegisterBlockProvidersEvent();
        $this->trigger(self::REGISTER_BLOCK_PROVIDERS, $e);
        $this->_providers = $e->getProviders();
    }
}