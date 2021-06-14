<?php 

namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\DisplayMatrixException;
use Ryssbowh\CraftThemes\exceptions\FieldException;
use Ryssbowh\CraftThemes\interfaces\FieldInterface;
use Ryssbowh\CraftThemes\interfaces\MatrixInterface;
use Ryssbowh\CraftThemes\models\DisplayMatrixType;
use Ryssbowh\CraftThemes\models\ViewMode;
use Ryssbowh\CraftThemes\records\DisplayRecord;
use craft\base\Field as BaseField;
use craft\elements\MatrixBlock;

class Matrix extends CraftField implements MatrixInterface
{
    private $_types;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            ['types', 'safe'],
        ]);
    }

    /**
     * @inheritDoc
     */
    public static function getType(): string
    {
        return 'matrix';
    }

    /**
     * @inheritDoc
     */
    public static function create(array $config): FieldInterface
    {
        $class = get_called_class();
        $field = new $class;
        $attributes = $field->safeAttributes();
        $config = array_intersect_key($config, array_flip($attributes));
        $field->setAttributes($config);
        if ($config['types'] ?? null) {
            $field->types = static::buildMatrixTypes($config['types']);
        }
        if (!isset($config['options']) and $field->displayer) {
            $field->options = $field->displayer->options->toArray();
        }
        return $field;
    }

    /**
     * @inheritDoc
     */
    public static function createNew(?BaseField $craftField = null): FieldInterface
    {
        $types = [];
        foreach ($craftField->getBlockTypes() as $type) {
            $types[] = [
                'type' => $type,
                'fields' => array_map(function ($field) {
                    return MatrixField::buildConfig($field);
                }, $type->getFields())
            ];
        }
        $config = static::buildConfig($craftField);
        $config['types'] = $types;
        return static::create($config);
    }

    /**
     * @inheritDoc
     */
    public static function save(array $data): bool
    {
        $matrix = Themes::$plugin->fields->getRecordByUid($data['uid']);
        $types = $data['types'] ?? [];
        unset($data['types']);
        $field = \Craft::$app->fields->getFieldByUid($data['craft_field_id']);
        $data['craft_field_id'] = $field->id;
        $data['craft_field_class'] = get_class($field);
        $matrix->setAttributes($data, false);
        $res = $matrix->save(false);
        foreach ($types as $typeData) {
            $fields = $typeData['fields'] ?? [];
            unset($typeData['fields']);
            $type = Themes::$plugin->matrix->getMatrixBlockTypeByUid($typeData['type_uid']);
            foreach ($fields as $order => $fieldData) {
                $field = Themes::$plugin->fields->getRecordByUid($fieldData['uid']);
                $fieldData['craft_field_id'] = \Craft::$app->fields->getFieldByUid($fieldData['craft_field_id'])->id;
                $field->setAttributes($fieldData, false);
                $field->save(false);
                $pivot = Themes::$plugin->matrix->getMatrixPivotRecord($type->id, $matrix->id, $field->id);
                $pivot->field_id = $field->id;
                $pivot->parent_id = $matrix->id;
                $pivot->matrix_type_id = $type->id;
                $pivot->order = $order;
                $pivot->save(false);
            }
        }
        return $res;
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): array
    {
        $config = parent::getConfig();
        $config['types'] = array_map(function ($type) {
            return $type->getConfig();
        }, $this->types);
        return $config;
    }

    /**
     * @inheritDoc
     */
    public function getTypes(): array
    {
        if ($this->_types === null) {
            if ($this->craftField === null) {
                throw DisplayException::noCraftField($this);
            }
            foreach ($this->craftField->getBlockTypes() as $type) {
                $this->_types[$type->handle] = new DisplayMatrixType([
                    'type' => $type,
                    'fields' => Themes::$plugin->matrix->getForMatrixType($type, $this)
                ]);
            }
        }
        return $this->_types;
    }

    /**
     * @inheritDoc
     */
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

    /**
     * Types setter
     * 
     * @param array $types
     */
    public function setTypes(array $types)
    {
        $this->_types = $types;
    }

    /**
     * @inheritDoc
     */
    public function fields()
    {
        return array_merge(parent::fields(), ['types']);
    }

    /**
     * Build matrix types from an array of data
     * 
     * @param  array  $data
     * @return array
     */
    protected static function buildMatrixTypes(array $data): array
    {
        $types = [];
        foreach ($data as $typeData) {
            $type_id = $typeData['type_id'] ?? $typeData['type']['id'];
            $type = \Craft::$app->matrix->getBlockTypeById($type_id);
            $fields = [];
            foreach ($typeData['fields'] as $fieldData) {
                $field = Themes::$plugin->fields->create($fieldData);
                if (!isset($fieldData['options']) and $field->displayer) {
                    $field->options = $field->displayer->options->toArray();
                }
                $fields[] = $field;
            }
            $types[] = new DisplayMatrixType([
                'type' => $type,
                'fields' => $fields
            ]);
        }
        return $types;
    }
}