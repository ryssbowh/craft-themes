<?php

namespace Ryssbowh\CraftThemes\records;

use Ryssbowh\CraftThemes\models\DisplayField;
use craft\db\ActiveRecord;

class FieldRecord extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%themes_fields}}';
    }

    public function toModel(): DisplayField
    {
        $model = new DisplayField($this->getAttributes());
        $model->options = json_decode($this->options, true);
        return $model;
    }
}