<?php 

namespace Ryssbowh\CraftThemes\blockProviders;

use Ryssbowh\CraftThemes\blocks\ContentBlock;
use Ryssbowh\CraftThemes\blocks\TemplateBlock;
use Ryssbowh\CraftThemes\blocks\TwigBlock;

class SystemBlockProvider extends BlockProvider
{
	public $blocks = [
        TemplateBlock::class,
		ContentBlock::class,
        TwigBlock::class,
	];

	public $handle = 'system';

	public $name = 'System';

	public static function getName(): string
	{
		return \Craft::t('themes', 'System');
	}
}