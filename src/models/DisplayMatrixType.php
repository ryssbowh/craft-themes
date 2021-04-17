<?php 

namespace Ryssbowh\CraftThemes\models;

use craft\base\Model;
use craft\models\MatrixBlockType;

class DisplayMatrixType extends Model
{
    public $fields = [];
    public $type_id;
    private $_type;

    public function getConfig()
    {
        return [
            'type_uid' => $this->type->uid,
            'fields' => array_map(function ($field) {
                return $field->getConfig();
            }, $this->fields)
        ];
    }

    public function getType(): MatrixBlockType
    {
        if ($this->_type === null) {
            $this->_type = \Craft::$app->matrix->getBlockTypeById($this->type_id);
        }
        return $this->_type;
    }

    public function setType(MatrixBlockType $type)
    {
        $this->_type = $type;
        $this->type_id = $type->id;
    }

    public function fields()
    {
        return array_merge(parent::fields(), ['type']);
    }
}