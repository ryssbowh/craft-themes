<?php

namespace Ryssbowh\CraftThemes\records;

use Ryssbowh\CraftThemes\models\DisplayMatrix;
use craft\db\ActiveRecord;

class MatrixRecord extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%themes_matrix}}';
    }

    public function toModel(): DisplayMatrix
    {
        return new DisplayMatrix($this->getAttributes());
    }

    public function getFields()
    {
        return $this->hasMany(FieldRecord::className(), ['id' => 'field_id'])
            ->viaTable('themes_pivot_matrix_field', ['matrix_id' => 'id'])
            ->orderBy(['themes_pivot_matrix_field.order' => SORT_ASC]);
    }
}