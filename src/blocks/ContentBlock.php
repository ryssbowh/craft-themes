<?php 

namespace Ryssbowh\CraftThemes\blocks;

class ContentBlock extends Block
{
	public function getName(): string
	{
		return 'Content';
	}

	public function getHandle(): string
	{
		return 'content';
	}
}