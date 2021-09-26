<?php 

namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\DisplayMatrixException;
use Ryssbowh\CraftThemes\exceptions\FieldException;
use Ryssbowh\CraftThemes\interfaces\FieldInterface;
use Ryssbowh\CraftThemes\interfaces\MatrixInterface;
use Ryssbowh\CraftThemes\models\DisplayMatrixType;
use Ryssbowh\CraftThemes\records\DisplayRecord;
use Ryssbowh\CraftThemes\records\FieldRecord;
use Ryssbowh\CraftThemes\records\MatrixPivotRecord;
use craft\base\Field as BaseField;
use craft\elements\MatrixBlock;
use craft\fields\Matrix as CraftMatrix;

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
    public static function forField(): string
    {
        return CraftMatrix::class;
    }

    /**
     * @inheritDoc
     */
    public function onCraftFieldChanged(BaseField $craftField): bool
    {
        $oldTypes = $this->types;
        $newTypes = [];
        foreach ($craftField->getBlockTypes() as $craftType) {
            if (isset($oldTypes[$craftType->handle])) {
                $type = $oldTypes[$craftType->handle];
            } else {
                //Type doesn't exist on the Matrix, creating a new one
                $type = new DisplayMatrixType([
                    'type' => $craftType,
                    'fields' => []
                ]);
            }
            $fields = [];
            foreach ($craftType->fields as $craftField) {
                try {
                    $oldField = $type->getFieldById($craftField->id);
                } catch (\Throwable $e) {
                    $oldField = null;
                }
                if (!$oldField) {
                    //New field was added to the block type, creating new field
                    $field = MatrixField::createFromField($craftField);
                } else if ($oldField->craft_field_class != get_class($craftField)) {
                    //Field has changed class, creating a new one 
                    //and copying old fields attributes
                    $field = MatrixField::createFromField($craftField);
                    $field->id = $oldField->id;
                    $field->uid = $oldField->uid;
                    $field->labelHidden = $oldField->labelHidden;
                    $field->labelVisuallyHidden = $oldField->labelVisuallyHidden;
                    $field->visuallyHidden = $oldField->visuallyHidden;
                    $field->hidden = $field->hidden ?: $oldField->hidden;
                } else {
                    //Field hasn't changed
                    $field = $oldField;
                }
                $field->matrix = $this;
                $fields[] = $field;
            }
            $type->fields = $fields;
            $newTypes[$craftType->handle] = $type;
        }
        $this->types = $newTypes;
        return true;
    }

    /**
     * @inheritDoc
     */
    public static function create(?array $config = null): FieldInterface
    {
        $class = get_called_class();
        $field = new $class;
        $attributes = $field->safeAttributes();
        $config = array_intersect_key($config, array_flip($attributes));
        $field->setAttributes($config);
        if ($config['types'] ?? null) {
            $field->types = static::buildMatrixTypes($config['types'], $field);
        }
        if (!isset($config['options']) and $field->displayer) {
            $field->options = $field->displayer->options->toArray();
        }
        return $field;
    }

    /**
     * @inheritDoc
     */
    public static function createFromField(BaseField $craftField): FieldInterface
    {
        $config = static::buildConfig($craftField);
        $config['types'] = self::buildMatrixTypesConfig($craftField);
        return static::create($config);
    }

    /**
     * @inheritDoc
     */
    public static function save(string $uid, array $data): bool
    {
        $matrix = Themes::$plugin->fields->getRecordByUid($uid);
        $types = $data['types'] ?? [];
        unset($data['types']);
        $field = \Craft::$app->fields->getFieldByUid($data['craft_field_id']);
        $data['craft_field_id'] = $field->id;
        $data['craft_field_class'] = get_class($field);
        $matrix->setAttributes($data, false);
        $res = $matrix->save(false);
        $pivotIds = [];
        foreach ($types as $typeData) {
            $fields = $typeData['fields'] ?? [];
            unset($typeData['fields']);
            $type = Themes::$plugin->matrix->getMatrixBlockTypeByUid($typeData['type_uid']);
            foreach ($fields as $order => $fieldData) {
                $field = Themes::$plugin->fields->getRecordByUid($fieldData['fieldUid']);
                unset($fieldData['fieldUid']);
                $fieldData['craft_field_id'] = \Craft::$app->fields->getFieldByUid($fieldData['craft_field_id'])->id;
                $field->setAttributes($fieldData, false);
                $field->save(false);
                $pivot = Themes::$plugin->matrix->getMatrixPivotRecord($type->id, $matrix->id, $field->id);
                $pivot->field_id = $field->id;
                $pivot->parent_id = $matrix->id;
                $pivot->matrix_type_id = $type->id;
                $pivot->order = $order;
                $pivot->save(false);
                $pivotIds[] = $pivot->id;
            }
        }
        //deleting old field records
        $oldRecords = MatrixPivotRecord::find()
            ->where(['parent_id' => $matrix->id])
            ->andWhere(['not in', 'id', $pivotIds])
            ->all();
        $fieldIds = [];
        foreach ($oldRecords as $record) {
            $fieldIds[] = $record->field_id;
        }
        \Craft::$app->getDb()->createCommand()
            ->delete(FieldRecord::tableName(), ['in', 'id', $fieldIds])
            ->execute();
        return $res;
    }

    /**
     * @inheritDoc
     */
    public static function delete(string $uid, array $data)
    {
        parent::delete($uid, $data);
        $fieldUids = [];
        foreach ($data['types'] ?? [] as $typeData) {
            foreach ($typeData['fields'] ?? [] as $fieldData) {
                $fieldUids[] = $fieldData['fieldUid'];
            }
        }
        \Craft::$app->getDb()->createCommand()
            ->delete(FieldRecord::tableName(), ['in', 'uid', $fieldUids])
            ->execute();
    }

    /**
     * @inheritDoc
     */
    public function populateFromPost(array $data)
    {
        $attributes = $this->safeAttributes();
        $data = array_intersect_key($data, array_flip($attributes));
        $data['types'] = $this->buildMatrixTypes($data['types'], $this);
        $this->setAttributes($data);
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
     * Creates the types config for a Matrix field
     * 
     * @param  Matrix $craftField
     * @return array
     */
    protected static function buildMatrixTypesConfig(CraftMatrix $craftField): array
    {
        $types = [];
        foreach ($craftField->getBlockTypes() as $type) {
            $types[$type->handle] = [
                'type' => $type,
                'fields' => array_map(function ($field) {
                    return MatrixField::buildConfig($field);
                }, $type->getFields())
            ];
        }
        return $types;
    }

    /**
     * Build matrix types from an array of data
     * 
     * @param  array  $data
     * @param  Matrix $matrix
     * @return array
     */
    protected static function buildMatrixTypes(array $data, Matrix $matrix): array
    {
        $types = [];
        foreach ($data as $typeData) {
            $type_id = $typeData['type_id'] ?? $typeData['type']['id'];
            $type = \Craft::$app->matrix->getBlockTypeById($type_id);
            $fields = [];
            foreach ($typeData['fields'] as $fieldData) {
                $field = Themes::$plugin->fields->create($fieldData);
                $field->matrix = $matrix;
                if (!isset($fieldData['options']) and $field->displayer) {
                    $field->options = $field->displayer->options->toArray();
                }
                $fields[] = $field;
            }
            $types[$type->handle] = new DisplayMatrixType([
                'type' => $type,
                'fields' => $fields
            ]);
        }
        return $types;
    }
}