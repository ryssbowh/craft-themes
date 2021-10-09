<?php

namespace Ryssbowh\CraftThemes\records;

use craft\db\ActiveRecord;

class TablePivotRecord extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%themes_pivot_table}}';
    }
}