<?php 

namespace Ryssbowh\CraftThemes\models;

use craft\base\Model;

class Region extends Model
{
	public $handle = '';

	public $name = '';

	public $width = '100%';

	public $blocks = [];

	public function getTemplateSuggestions(): array
	{
		return ['regions/region-' . $this->handle, 'regions/region'];
	}
}