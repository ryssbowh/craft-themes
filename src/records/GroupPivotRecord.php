<?php

namespace Ryssbowh\CraftThemes\records;

use Ryssbowh\CraftThemes\models\DisplayMatrix;
use craft\db\ActiveRecord;

class GroupPivotRecord extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%themes_pivot_group}}';
    }
}