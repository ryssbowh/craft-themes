<?php 

namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\events\RegisterBlockProvidersEvent;
use Ryssbowh\CraftThemes\exceptions\BlockProviderException;
use Ryssbowh\CraftThemes\interfaces\BlockProviderInterface;
use craft\base\Component;

class BlockProvidersService extends Component
{
	const REGISTER_BLOCK_PROVIDERS = 'block_providers';

	protected $providers = [];

	public function init()
	{
		parent::init();
		$e = new RegisterBlockProvidersEvent();
        $this->trigger(self::REGISTER_BLOCK_PROVIDERS, $e);
        $this->providers = $e->getProviders();
	}

	public function getAll(): array
	{
		return $this->providers;
	}

	public function getByHandle(string $handle): BlockProviderInterface
	{
		if (isset($this->providers[$handle])) {
			return $this->providers[$handle];
		}
		throw BlockProviderException::notDefined($handle);
	}
}