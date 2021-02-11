<?php 

namespace Ryssbowh\CraftThemes\events;

use Ryssbowh\CraftThemes\exceptions\BlockProviderException;
use Ryssbowh\CraftThemes\interfaces\BlockProviderInterface;
use yii\base\Event;

class RegisterBlockProvidersEvent extends Event
{
	protected $providers = [];

	public function add(BlockProviderInterface $provider)
	{
		$this->providers[$provider->handle] = $provider;
		return $this;
	}

	public function getProviders(): array
	{
		return $this->providers;
	}
}