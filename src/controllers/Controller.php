<?php 

namespace Ryssbowh\CraftThemes\controllers;

use Ryssbowh\CraftThemes\Themes;
use craft\web\Controller as CraftController;

class Controller extends CraftController
{
	protected $registry;
	protected $blockProviders;
	protected $blocks;
	protected $layouts;

	public function init()
	{
		parent::init();
		$this->registry = Themes::$plugin->registry;
		$this->blockProviders = Themes::$plugin->blockProviders;
		$this->blocks = Themes::$plugin->blocks;
		$this->layouts = Themes::$plugin->layouts;
	}
}