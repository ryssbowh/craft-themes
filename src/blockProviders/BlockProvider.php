<?php 

namespace Ryssbowh\CraftThemes\blockProviders;

use Ryssbowh\CraftThemes\exceptions\BlockProviderException;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\interfaces\BlockProviderInterface;
use Ryssbowh\CraftThemes\services\BlockService;
use craft\base\Component;

abstract class BlockProvider extends Component implements BlockProviderInterface
{
	public $blocks = [];

	public $handle;

	public $name;

	public function init()
	{
		parent::init();
		if (!$this->handle) {
			throw BlockProviderException::noHandle(get_called_class());
		}
		if (!$this->name) {
			throw BlockProviderException::noName(get_called_class());
		}
	}

	public function getBlocksObjects(): array
	{
		$_this = $this;
		return array_map(function ($class) use ($_this) {
			return $_this->loadBlock($class);
		}, $this->blocks);
	}

	public function getBlock(string $handle, array $attributes = []): BlockInterface
	{
		foreach ($this->blocks as $class) {
			if ($class::$handle == $handle) {
				return $this->loadBlock($class, $attributes);
			}
		}
		throw BlockProviderException::noBlock(get_called_class(), $handle);
	}

	public function addBlock(string $blockClass): BlockProviderInterface
	{
		$this->blockClasses[] = $block;
		if ($this->loaded) {
			$this->loadBlock($blockClass);
		}
		return $this;
	}

	public function fields()
	{
		return array_merge(parent::fields(), ['blocksObjects']);
	}

	protected function loadBlock(string $class, $attributes = [])
	{
		$attributes['provider'] = $this->handle;
		return BlockService::createBlock($class, $attributes);
	}
}