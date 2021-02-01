<?php 

namespace Ryssbowh\CraftThemes\blockProviders;

use Ryssbowh\CraftThemes\blocks\ContentBlock;
use Ryssbowh\CraftThemes\blocks\TestBlock;
use Ryssbowh\CraftThemes\blocks\ThingBlock;

class SystemBlockProvider extends BlockProvider
{
	protected $blocks = [
		'test_hello' => TestBlock::class,
		'thing' => ThingBlock::class,
		'content' => ContentBlock::class,
	];

	public static function getName(): string
	{
		return \Craft::t('themes', 'System');
	}

	public static function getHandle(): string
	{
		return 'system';
	}
}