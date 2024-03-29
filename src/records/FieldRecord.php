<?php
namespace Ryssbowh\CraftThemes\records;

use craft\db\ActiveRecord;

class FieldRecord extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%themes_fields}}';
    }

    public function getAttribute($name)
    {
        $value = parent::getAttribute($name);
        if ($name == 'options') {
            return json_decode($value, true);
        }
        return $value;
    }

    public function getAttributes($names = null, $except = [])
    {
        $values = parent::getAttributes($names, $except);
        if (isset($values['options'])) {
            $values['options'] = $this->getAttribute('options');
        }
        return $values;
    }
}