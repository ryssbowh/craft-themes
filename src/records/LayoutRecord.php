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

    public function toModel(): Model
    {
        $attributes = $this->getAttributes();
        return Layout::create($attributes);
    }
}