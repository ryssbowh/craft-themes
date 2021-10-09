<?php

namespace Ryssbowh\CraftThemes\records;

use craft\db\ActiveRecord;

class DisplayRecord extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%themes_displays}}';
    }
}