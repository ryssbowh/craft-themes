<?php

namespace Ryssbowh\CraftThemes\records;

use Ryssbowh\CraftThemes\models\LayoutLine;
use craft\db\ActiveRecord;

class LayoutLineRecord extends ActiveRecord
{
	public static function tableName()
	{
		return '{{%theme_block_layouts}}';
	}

	public function toModel()
	{
		return new LayoutLine($this->getAttributes());
	}
}