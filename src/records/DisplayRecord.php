<?php

namespace Ryssbowh\CraftThemes\records;

use craft\db\ActiveRecord;

class DisplayRecord extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%themes_displays}}';
    }

    public function getViewMode()
    {
        return $this->hasOne(ViewModeRecord::className(), ['id' => 'viewMode_id']);
    }

    public function getField()
    {
        return $this->hasOne(FieldRecord::className(), ['display_id' => 'id']);
    }

    public function getGroup()
    {
        return $this->hasOne(GroupRecord::className(), ['display_id' => 'id']);
    }
}