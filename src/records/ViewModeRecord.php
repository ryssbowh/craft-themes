<?php

namespace Ryssbowh\CraftThemes\records;

use Ryssbowh\CraftThemes\models\ViewMode;
use craft\db\ActiveRecord;

class ViewModeRecord extends ActiveRecord
{
	public static function tableName()
	{
		return '{{%themes_view_modes}}';
	}

	public function toModel(): ViewMode
	{
		return new ViewMode($this->getAttributes());
	}
}