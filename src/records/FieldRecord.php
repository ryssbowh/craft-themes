<?php

namespace Ryssbowh\CraftThemes\records;

use Ryssbowh\CraftThemes\models\Field;
use craft\db\ActiveRecord;

class FieldRecord extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%themes_fields}}';
    }

    public function toModel(): Field
    {
        return new Field($this->getAttributes());
    }
}