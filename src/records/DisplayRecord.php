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
        if ($this->isRelationPopulated('viewMode')) {
            $model->viewMode = $this->viewMode->toModel();
        }
        if ($this->isRelationPopulated('group')) {
            $model->item = $this->group->toModel();
        } else if ($this->isRelationPopulated('field')) {
            $model->item = $this->field->toModel();
        }
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
}