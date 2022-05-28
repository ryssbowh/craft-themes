<?php
namespace Ryssbowh\CraftThemes\records;

use craft\db\ActiveRecord;

class ParentPivotRecord extends ActiveRecord
{
    /**
     * @inheritDoc
     */
    public static function tableName()
    {
        return '{{%themes_pivot_parents}}';
    }

    /**
     * Get decoded data
     * 
     * @return array
     * @since  3.2.0
     */
    public function getDecodedData()
    {
        return json_decode($this->data, true);
    }

    /**
     * Get parent relation
     * 
     * @return ActiveQueryInterface
     */
    public function getParent()
    {
        return $this->hasOne(FieldRecord::className(), ['id' => 'parent_id']);

    }

    /**
     * Get field relation
     * 
     * @return ActiveQueryInterface
     */
    public function getField()
    {
        return $this->hasOne(FieldRecord::className(), ['id' => 'field_id']);

    }
}