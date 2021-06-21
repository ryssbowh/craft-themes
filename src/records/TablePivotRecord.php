<?php

namespace Ryssbowh\CraftThemes\records;

use craft\db\ActiveRecord;

class TablePivotRecord extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%themes_pivot_table}}';
    }

    public function getField()
    {
        return $this->hasOne(FieldRecord::className(), ['id' => 'field_id']);
    }

    public function getTable()
    {
        return $this->hasOne(FieldRecord::className(), ['id' => 'table_id']);
    }
}