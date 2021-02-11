<?php 

namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\exceptions\BlockException;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\interfaces\SuggestsTemplates;
use craft\base\Model;

abstract class Block extends Model implements BlockInterface, SuggestsTemplates
{
	public static $handle;
	public $name = '';
	public $defaultOptions = [];

	public $id;
	public $region;
	public $theme;
	public $provider;
	public $order;
	public $ignore;
	public $active;
	public $options;
	public $dateCreated;
	public $dateUpdated;
	public $uid;

	public function init()
	{
		parent::init();
		if (!$this::$handle) {
			throw BlockException::noHandle(get_called_class());
		}
		if (!$this->provider) {
			throw BlockException::noProvider(get_called_class());
		}
		if (!$this->name) {
			throw BlockException::noName(get_called_class());
		}
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
			'handle' => $this->handle,
			'provider' => $this->provider,
			'order' => $this->order,
			'theme' => $this->theme,
			'active' => $this->active
		];
	}

	public function rules()
	{
		return [
			[['region', 'handle', 'provider', 'order', 'active', 'theme'], 'required'],
			[['region', 'handle', 'provider', 'theme'], 'string'],
			['active', 'boolean'],
			['order', 'number']
		];
	}

	public function getProvider(): BlockProviderInterface
	{
		return Themes::$plugin->blockProviders->getByHandle($this->provider);
	}

	public function getHandle(): string
	{
		return $this::$handle;
	}

	public function getMachineName(): string
	{
		return $this->provider . '_' . $this::$handle;
	}

	public function hasOptions(): bool
	{
		return sizeof($this->hasSettings) > 0;
	}

	public function getOptionsHtml(): string
	{
		return '';
	}

	public function getTemplateSuggestions(): array
	{
		return ['blocks/block-' . $this->getMachineName(), 'blocks/block'];
	}

	public function fields()
	{
		return array_merge(parent::fields(), ['handle']);
	}
}