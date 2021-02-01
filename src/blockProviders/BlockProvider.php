<?php 

namespace Ryssbowh\CraftThemes\blockProviders;

use Ryssbowh\CraftThemes\exceptions\BlockProviderException;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\interfaces\BlockProviderInterface;
use Ryssbowh\CraftThemes\services\BlockService;
use craft\base\Component;

abstract class BlockProvider extends Component implements BlockProviderInterface
{
	protected $blocks = [];

	public function getBlocks(): array
	{
		return array_map(function ($block) {
			return BlockService::createBlock($block);
		}, $this->blocks);
	}

	public function getBlock(string $handle): BlockInterface
	{
		if (!isset($this->blocks[$handle])) {
			throw BlockProviderException::noBlock($this->getHandle(), $handle);
		}
		return BlockService::createBlock($this->blocks[$handle]);
	}

	public function addBlock($block): BlockProviderInterface
	{
		$this->blocks[$blockClass::getHandle()] = $block;
		return $this;
	}
}