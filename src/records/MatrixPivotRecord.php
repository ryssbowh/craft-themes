<?php

namespace Ryssbowh\CraftThemes\records;

use Ryssbowh\CraftThemes\models\DisplayMatrix;
use craft\db\ActiveRecord;

class MatrixPivotRecord extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%themes_pivot_matrix}}';
    }

    public function getField()
    {
        return $this->hasOne(FieldRecord::className(), ['id' => 'field_id']);
    }

    public function getParent()
    {
        return $this->hasOne(FieldRecord::className(), ['id' => 'parent_id']);
    }
}