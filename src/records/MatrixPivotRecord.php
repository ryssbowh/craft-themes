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
}