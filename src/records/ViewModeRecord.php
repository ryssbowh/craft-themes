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

    public function getLayout()
    {
        return $this->hasOne(LayoutRecord::className(), ['layout_id' => 'id']);
    }
}