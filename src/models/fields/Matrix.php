<?php
namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\DisplayMatrixException;
use Ryssbowh\CraftThemes\exceptions\FieldException;
use Ryssbowh\CraftThemes\helpers\ProjectConfigHelper;
use Ryssbowh\CraftThemes\interfaces\FieldInterface;
use Ryssbowh\CraftThemes\interfaces\MatrixInterface;
use Ryssbowh\CraftThemes\models\DisplayMatrixType;
use Ryssbowh\CraftThemes\records\DisplayRecord;
use Ryssbowh\CraftThemes\records\FieldRecord;
use Ryssbowh\CraftThemes\records\MatrixPivotRecord;
use craft\base\Field as BaseField;
use craft\elements\MatrixBlock;
use craft\fields\Matrix as CraftMatrix;
use craft\helpers\StringHelper;

/**
 * Handles a Craft matrix field
 */
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
    public function hasErrors($attribute = null)
    {
        if ($attribute !== null) {
            return parent::hasErrors($attribute);
        }
        foreach ($this->types as $type) {
            foreach ($type->fields as $field) {
                if ($field->hasErrors()) {
                    return true;
                }
            }
        }
        return parent::hasErrors();
    }

    /**
     * @inheritDoc
     */
    public function afterValidate()
    {
        foreach ($this->types as $type) {
            foreach ($type->fields as $field) {
                $field->validate();
            }
        }
        parent::afterValidate();
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
    public function eagerLoad(string $prefix = '', int $level = 0): array
    {
        if (!$this->displayer) {
            return [];
        }
        if ($level >= Themes::$plugin->settings->maxEagerLoadLevel) {
            \Craft::info("Maximum eager loaging level (" . Themes::$plugin->settings->maxEagerLoadLevel . ') reached', __METHOD__);
            return [];
        }
        $with = [$prefix . $this->craftField->handle];
        foreach ($this->getTypes() as $type) {
            $typePrefix = $prefix . $this->craftField->handle . '.' . $type->type->handle . '::';
            foreach ($type->fields as $field) {
                $with = array_merge($with, $field->eagerLoad($typePrefix, $level + 1));
            }
        }
        return $with;
    }

    /**
     * @inheritDoc
     */
    public function onCraftFieldChanged(BaseField $craftField): bool
    {
        $oldTypes = $this->types;
        $newTypes = [];
        $fieldIdsToKeep = [];
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
                    //Field has changed class, creating a new one and copying old fields attributes
                    $field = MatrixField::createFromField($craftField);
                    $field->id = $oldField->id;
                    $field->uid = $oldField->uid;
                    $field->labelHidden = $oldField->labelHidden;
                    $field->labelVisuallyHidden = $oldField->labelVisuallyHidden;
                    $field->visuallyHidden = $oldField->visuallyHidden;
                    $field->hidden = $field->hidden ?: $oldField->hidden;
                    $fieldIdsToKeep[] = $field->id;
                } else {
                    //Field hasn't changed
                    $field = $oldField;
                    $fieldIdsToKeep[] = $field->id;
                }
                $field->matrix = $this;
                $fields[] = $field;
            }
            $type->fields = $fields;
            $newTypes[$craftType->handle] = $type;
        }
        $this->types = $newTypes;
        //Deleting all fields apart from those that haven't changed to make sure project config is synced
        //New fields will be created later when the matrix is saved
        $oldRecords = MatrixPivotRecord::find()
            ->where(['parent_id' => $this->id])
            ->andWhere(['not in', 'field_id', $fieldIdsToKeep])
            ->all();
        foreach ($oldRecords as $record) {
            $field = Themes::$plugin->fields->getById($record->field_id);
            Themes::$plugin->fields->delete($field);
        }
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
    public static function save(FieldInterface $field): bool
    {
        foreach ($field->types as $type) {
            foreach ($type->fields as $matrixField) {
                $matrixField->matrix = $field;
                Themes::$plugin->fields->save($matrixField);
            }
        }
        return parent::save($field);
    }

    /**
     * @inheritDoc
     */
    public static function handleChanged(string $uid, array $data)
    {
        parent::handleChanged($uid, $data);
        $matrix = Themes::$plugin->fields->getRecordByUid($uid);
        foreach ($data['types'] as $typeData) {
            $fields = $typeData['fields'] ?? [];
            ProjectConfigHelper::ensureFieldsProcessed($fields);
            $type = Themes::$plugin->matrix->getMatrixBlockTypeByUid($typeData['type_uid']);
            foreach ($fields as $order => $fieldUid) {
                $field = Themes::$plugin->fields->getRecordByUid($fieldUid);
                $pivot = Themes::$plugin->matrix->getMatrixPivotRecord($type->id, $matrix->id, $field->id);
                $pivot->order = $order;
                $pivot->save(false);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public static function delete(FieldInterface $field): bool
    {
        foreach ($field->types as $type) {
            foreach ($type->fields as $matrixField) {
                Themes::$plugin->fields->delete($matrixField);
            }
        }
        return parent::delete($field);
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
     * Get a field by handle
     * 
     * @param  MatrixBlock $block
     * @param  string      $handle
     * @return FieldInterface
     */
    public function getFieldByHandle(MatrixBlock $block, string $handle): ?FieldInterface
    {
        if (!isset($this->types[$block->type->handle])) {
            return null;
        }
        $type = $this->types[$block->type->handle];
        foreach ($type->fields as $field) {
            if ($field->craftField->handle == $handle) {
                return $field;
            }
        }
        return null;
    }

    /**
     * Get fields by handles
     * 
     * @param  MatrixBlock $block
     * @param  array       $handles
     * @return array
     */
    public function getFieldsByHandles(MatrixBlock $block, array $handles): array
    {
        if (!isset($this->types[$block->type->handle])) {
            return [];
        }
        $type = $this->types[$block->type->handle];
        $fields = [];
        foreach ($type->fields as $field) {
            if (in_array($field->craftField->handle, $handles)) {
                $fields[] = $field;
            }
        }
        return $fields;
    }

    /**
     * Get a field by uid
     * 
     * @param  MatrixBlock $block
     * @param  string      $uid
     * @return FieldInterface
     */
    public function getFieldByUid(MatrixBlock $block, string $uid): ?FieldInterface
    {
        if (!isset($this->types[$block->type->handle])) {
            return null;
        }
        $type = $this->types[$block->type->handle];
        foreach ($type->fields as $field) {
            if ($field->uid == $uid) {
                return $field;
            }
        }
        return null;
    }

    /**
     * Get fields by uids
     * 
     * @param  MatrixBlock $block
     * @param  array       $uids
     * @return array
     */
    public function getFieldsByUids(MatrixBlock $block, array $uids): array
    {
        if (!isset($this->types[$block->type->handle])) {
            return [];
        }
        $type = $this->types[$block->type->handle];
        $fields = [];
        foreach ($type->fields as $field) {
            if (in_array($field->uid, $uids)) {
                $fields[] = $field;
            }
        }
        return $fields;
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