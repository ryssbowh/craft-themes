<?php

namespace Ryssbowh\CraftThemes\records;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\models\layouts\Layout;
use craft\base\Model;
use craft\db\ActiveRecord;

class LayoutRecord extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%themes_layouts}}';
    }

    public function getViewModes()
    {
        return $this->hasMany(ViewModeRecord::className(), ['layout_id' => 'id']);
    }

    public function getBlocks()
    {
        return $this->hasMany(BlockRecord::className(), ['layout_id' => 'id']);
    }
}