<?php 

namespace Ryssbowh\CraftThemes\blocks;

class ThingBlock extends Block
{
	public function getName(): string
	{
		return 'I\'m a thing';
	}

	public function getHandle(): string
	{
		return 'thing';
	}
}