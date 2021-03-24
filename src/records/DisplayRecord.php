<?php

namespace Ryssbowh\CraftThemes\records;

use Ryssbowh\CraftThemes\models\Display;
use Ryssbowh\CraftThemes\services\DisplayService;
use craft\db\ActiveRecord;

class DisplayRecord extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%themes_displays}}';
    }

    public function toModel(): Display
    {
        $model = new Display($this->getAttributes());
        $model->viewMode = $this->viewMode->toModel();
        $model->item = $this->item->toModel();
        return $model;
    }

    public function getViewMode()
    {
        return $this->hasOne(ViewModeRecord::className(), ['id' => 'viewMode_id']);
    }

    public function getField()
    {
        return $this->hasOne(FieldRecord::className(), ['display_id' => 'id']);
    }

    public function getGroup()
    {
        return $this->hasOne(GroupRecord::className(), ['display_id' => 'id']);
    }

    public function getMatrix()
    {
        return $this->hasOne(MatrixRecord::className(), ['display_id' => 'id']);
    }

    public function getItem()
    {
        if  ($this->type == DisplayService::TYPE_FIELD) {
            return $this->field;
        } elseif ($this->type == DisplayService::TYPE_MATRIX) {
            return $this->matrix;
        }
        return $this->group;
    }
}