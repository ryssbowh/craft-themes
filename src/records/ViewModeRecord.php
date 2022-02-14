<?php
namespace Ryssbowh\CraftThemes\records;

use craft\db\ActiveRecord;

class ViewModeRecord extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%themes_view_modes}}';
    }
}