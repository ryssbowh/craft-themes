<?php

namespace Ryssbowh\CraftThemes\records;

use Ryssbowh\CraftThemes\models\DisplayGroup;
use craft\db\ActiveRecord;

class GroupRecord extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%themes_groups}}';
    }

    public function getFields()
    {
        return $this->hasMany(FieldRecord::className(), ['id' => 'field_id'])
            ->viaTable('themes_pivot_group_field', ['group_id' => 'id'])
            ->orderBy(['themes_pivot_group_field.order' => SORT_ASC]);
    }
}