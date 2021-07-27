<?php

namespace Ryssbowh\CraftThemes\records;

use craft\db\ActiveRecord;

class GroupRecord extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%themes_groups}}';
    }
}