<?php 

namespace Ryssbowh\CraftThemes\interfaces;

use Ryssbowh\CraftThemes\interfaces\BlockInterface;

interface BlockProviderInterface 
{
	public function addBlock(string $blockClass): BlockProviderInterface;

	public function getBlock(string $handle, array $attributes = []): BlockInterface;
}