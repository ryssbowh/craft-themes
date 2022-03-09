<?php
namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\interfaces\FieldInterface;
use craft\base\Model;
use craft\helpers\StringHelper;
use verbb\supertable\SuperTable;
use verbb\supertable\models\SuperTableBlockTypeModel;

/**
 * Class that handles a block type inside a super table
 */
class DisplaySuperTableType extends Model
{
    /**
     * @var array
     */
    public $fields = [];

    /**
     * The matrix block type id
     * @var int
     */
    public $type_id;

    /**
     * @var SuperTableBlockTypeModel
     */
    private $_type;

    /**
     * Get project config
     * 
     * @return array
     */
    public function getConfig(): array
    {
        return [
            'type_uid' => $this->type->uid,
            'fields' => array_map(function ($field) {
                return $field->uid;
            }, $this->fields)
        ];
    }

    /**
     * Super table block type getter
     * 
     * @return SuperTableBlockTypeModel
     */
    public function getType(): SuperTableBlockTypeModel
    {
        if ($this->_type === null) {
            $this->_type = SuperTable::$plugin->getService()->getBlockTypeById($this->type_id);
        }
        return $this->_type;
    }

    /**
     * Super table block type setter
     * 
     * @param SuperTableBlockTypeModel $type
     */
    public function setType(SuperTableBlockTypeModel $type)
    {
        $this->_type = $type;
        $this->type_id = $type->id;
    }

    /**
     * @inheritDoc
     */
    public function fields()
    {
        return array_merge(parent::fields(), ['type']);
    }

    /**
     * Find a field by craft field id
     * 
     * @param  int $id
     * @return ?FieldInterface
     */
    public function getFieldById(int $craftFieldId): ?FieldInterface
    {
        foreach ($this->fields as $field) {
            if ($field->craft_field_id == $craftFieldId) {
                return $field;
            }
        }
        return null;
    }
}