<?php 

namespace Ryssbowh\CraftThemes\blocks;

class TestBlock extends Block
{
	public function getName(): string
	{
		return 'Test hello';
	}

	public function getHandle(): string
	{
		return 'test_hello';
	}
}