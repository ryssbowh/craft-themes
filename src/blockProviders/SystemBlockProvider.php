<?php 

namespace Ryssbowh\CraftThemes\blockProviders;

use Ryssbowh\CraftThemes\blocks\ContentBlock;
use Ryssbowh\CraftThemes\blocks\TestBlock;
use Ryssbowh\CraftThemes\blocks\ThingBlock;

class SystemBlockProvider extends BlockProvider
{
	public $blocks = [
		TestBlock::class,
		ThingBlock::class,
		ContentBlock::class,
	];

	public $handle = 'system';

	public $name = 'System';

	public static function getName(): string
	{
		return \Craft::t('themes', 'System');
	}
}