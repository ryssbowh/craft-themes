<?php 

namespace Ryssbowh\CraftThemes\controllers;

class ThemesController
{
	public function indexAction()
	{
		$data['themes'] = Craft::plugin('theme');


        return $this->renderTemplate('themes/_index', $data);
	}
}