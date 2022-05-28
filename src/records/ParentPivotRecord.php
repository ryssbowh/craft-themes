<?php
namespace Ryssbowh\CraftThemes\records;

use craft\db\ActiveRecord;

class ParentPivotRecord extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%themes_pivot_parents}}';
    }

    public function getDecodedData()
    {
        return json_decode($this->data, true);
    }

    public function getParent()
    {
        return $this->hasOne(FieldRecord::className(), ['id' => 'parent_id']);

    }

    public function getField()
    {
        return $this->hasOne(FieldRecord::className(), ['id' => 'field_id']);

    }
}