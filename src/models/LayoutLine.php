<?php

namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\interfaces\BlockProviderInterface;
use craft\base\Model;

class LayoutLine extends Model
{
	public $id;
	public $region;
	public $theme;
	public $blockHandle;
	public $blockProvider;
	public $order;
	public $active;
	public $options;
	public $dateCreated;
	public $dateUpdated;
	public $uid;

	public function rules()
	{
		return [
			[['region', 'blockHandle', 'blockProvider', 'order', 'active', 'theme'], 'required'],
			[['region', 'blockHandle', 'blockProvider', 'theme'], 'string'],
			['active', 'boolean'],
			['order', 'number']
		];
	}

	/**
	 * Project config to be saved
	 * 
	 * @return array
	 */
	public function getConfig(): array
	{
		return [
			'region' => $this->region,
			'blockHandle' => $this->blockHandle,
			'blockProvider' => $this->blockProvider,
			'order' => $this->order,
			'theme' => $this->theme,
			'active' => $this->active
		];
	}

	public function getProvider(): BlockProviderInterface
	{
		return Themes::$plugin->blockProviders->getByHandle($this->blockProvider);
	}

	public function toBlock(): BlockInterface
	{
		return $this->getProvider()->getBlock($this->blockHandle);
	}
}