<?php 

namespace Ryssbowh\CraftThemes\blocks;

use Ryssbowh\CraftThemes\models\Block;

class TestBlock extends Block
{
	public $name = 'Test';

	public static $handle = 'test';

	public function getSettingsHtml(): string
	{
		return \Craft::$app->view->renderTemplate('themes/test');
	}
}