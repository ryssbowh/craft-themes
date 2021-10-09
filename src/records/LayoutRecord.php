<?php

namespace Ryssbowh\CraftThemes\records;

use craft\db\ActiveRecord;

class LayoutRecord extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%themes_layouts}}';
    }
}