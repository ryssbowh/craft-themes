<?php
namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\interfaces\FieldInterface;
use benf\neo\Plugin;
use benf\neo\models\BlockType;
use craft\base\Model;
use craft\helpers\StringHelper;

/**
 * Class that handles a block type inside a neo field
 *
 * @since 4.1.0
 */
class DisplayNeoType extends Model
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
     * @var BlockType
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
     * @return BlockType
     */
    public function getType(): BlockType
    {
        if ($this->_type === null) {
            $this->_type = Plugin::$plugin->blocks->getBlockById($this->type_id);
        }
        return $this->_type;
    }

    /**
     * Super table block type setter
     * 
     * @param BlockType $type
     */
    public function setType(BlockType $type)
    {
        $this->_type = $type;
        $this->type_id = $type->id;
    }

    /**
     * @inheritDoc
     */
    public function fields(): array
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