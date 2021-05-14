<?php 

namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\DisplayMatrixException;
use Ryssbowh\CraftThemes\exceptions\FieldException;
use Ryssbowh\CraftThemes\models\DisplayMatrixType;
use craft\elements\MatrixBlock;

class Matrix extends CraftField
{
    private $_types;

    /**
     * Project config to be saved
     * 
     * @return array
     */
    public function getConfig(): array
    {
        $config = parent::getConfig();
        $config['types'] = array_map(function ($type) {
            return $type->getConfig();
        }, $this->types);
        return $config;
    }

    public function getTypes(): array
    {
        if ($this->_types === null) {
            if ($this->craftField === null) {
                throw DisplayException::noCraftField($this);
            }
            foreach ($this->craftField->getBlockTypes() as $type) {
                $this->_types[$type->handle] = new DisplayMatrixType([
                    'type' => $type,
                    'fields' => Themes::$plugin->fields->getForMatrixType($type, $this)
                ]);
            }
        }
        return $this->_types;
    }

    public function getVisibleFields(MatrixBlock $block): array
    {
        if (!isset($this->types[$block->type->handle])) {
            return [];
        }
        $type = $this->types[$block->type->handle];
        return array_filter($type->fields, function ($field) {
            return $field->isVisible();
        });
    }

    public function getTypeById(int $id): DisplayMatrixType
    {
        foreach ($this->types as $type) {
            if ($type->type_id == $id) {
                return $type;
            }
        }
        throw DisplayMatrixException::noTypeWithId($id);
    }

    public function setTypes(array $types)
    {
        $this->_types = $types;
    }

    public function fields()
    {
        return array_merge(parent::fields(), ['types']);
    }
}