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
use Ryssbowh\CraftThemes\records\ParentPivotRecord;
use Ryssbowh\CraftThemes\services\DisplayerCacheService;
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
    public function eagerLoad(string $prefix = '', int $level = 0, array &$dependencies = []): array
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
            $typePrefix = $prefix . $this->craftField->handle . '.' . $type->type->handle . ':';
            foreach ($type->fields as $field) {
                $dependencies[] = DisplayerCacheService::DISPLAYER_CACHE_TAG . '::' . $field->id;
                $with = array_merge($with, $field->eagerLoad($typePrefix, $level + 1));
            }
        }
        return $with;
    }

    /**
     * @inheritDoc
     */
    public function rebuild(): bool
    {
        $oldTypes = $this->types;
        $newTypes = [];
        $fieldIdsToKeep = [];
        $hasChanged = false;
        foreach ($this->craftField->getBlockTypes() as $craftType) {
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
                $oldField = null;
                try {
                    $oldField = $type->getFieldById($craftField->id);
                } catch (\Throwable $e) {}
                if (!$oldField) {
                    //New field was added to the block type, creating new field
                    $field = Themes::$plugin->fields->createFromField($craftField);
                    $hasChanged = true;
                } else if ($oldField->craft_field_class != get_class($craftField)) {
                    //Field has changed class, creating a new one and copying old fields attributes
                    $field = Themes::$plugin->fields->createFromField($craftField);
                    $field->id = $oldField->id;
                    $field->uid = $oldField->uid;
                    $field->labelHidden = $oldField->labelHidden;
                    $field->labelVisuallyHidden = $oldField->labelVisuallyHidden;
                    $field->visuallyHidden = $oldField->visuallyHidden;
                    $field->hidden = $field->hidden ?: $oldField->hidden;
                    $fieldIdsToKeep[] = $field->id;
                    $hasChanged = true;
                } else {
                    //Field hasn't changed but forwarding the change to sub fields, in case they have things to change
                    $field = $oldField;
                    $hasChanged = ($hasChanged or $field->rebuild());
                    $fieldIdsToKeep[] = $field->id;
                }
                $field->parent = $this;
                $fields[] = $field;
            }
            $type->fields = $fields;
            $newTypes[$craftType->handle] = $type;
        }
        $this->types = $newTypes;
        return $hasChanged;
    }

    /**
     * @inheritDoc
     */
    public function populateFromData(array $data)
    {
        $data = array_intersect_key($data, array_flip($this->safeAttributes()));
        if ($data['types'] ?? null) {
            $data['types'] = $this->buildMatrixTypes($data['types']);
        }
        $this->setAttributes($data);
    }

    /**
     * @inheritDoc
     */
    public static function buildConfig(BaseField $craftField): array
    {
        $config = parent::buildConfig($craftField);
        $types = [];
        foreach ($craftField->getBlockTypes() as $type) {
            $types[$type->handle] = [
                'type' => $type,
                'fields' => array_map(function ($field) {
                    return Themes::$plugin->fields->buildConfig($field);
                }, $type->getFields())
            ];
        }
        $config['types'] = $types;
        return $config;
    }

    /**
     * @inheritDoc
     */
    public static function save(FieldInterface $field): bool
    {
        foreach ($field->types as $type) {
            foreach ($type->fields as $matrixField) {
                // $matrixField->parent = $field;
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
        $pivotsToKeep = [];
        foreach ($data['types'] as $typeData) {
            $fields = $typeData['fields'] ?? [];
            ProjectConfigHelper::ensureFieldsProcessed($fields);
            foreach ($fields as $order => $fieldUid) {
                $field = Themes::$plugin->fields->getRecordByUid($fieldUid);
                $pivot = Themes::$plugin->fields->getParentPivotRecord($matrix->id, $field->id);
                $pivot->order = $order;
                $pivot->save(false);
                $pivotsToKeep[] = $pivot->id;
            }
        }
        //Delete unused pivots
        $children = Themes::$plugin->fields->getChildrenPivots($matrix->id);
        foreach ($children as $pivot) {
            if (!in_array($pivot->id, $pivotsToKeep)) {
                $pivot->delete();
                try {
                    $field = Themes::$plugin->fields->getById($pivot->field_id);
                    Themes::$plugin->fields->delete($field);
                } catch (\Throwable $e) {}
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
            $this->_types = [];
            foreach ($this->craftField->getBlockTypes() as $type) {
                $fields = [];
                foreach ($type->fields as $field) {
                    if ($field = Themes::$plugin->fields->getChild($this, $field)) {
                        $fields[] = $field;
                    }
                }
                $this->_types[$type->handle] = new DisplayMatrixType([
                    'type' => $type,
                    'fields' => $fields
                ]);
            }
        }
        return $this->_types;
    }

    /**
     * @inheritDoc
     */
    public function setTypes(array $types)
    {
        $this->_types = $types;
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
     * @inheritDoc
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
     * @inheritDoc
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
     * @inheritDoc
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
     * @inheritDoc
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
    protected function buildMatrixTypes(array $data): array
    {
        $types = [];
        foreach ($data as $typeData) {
            $type_id = $typeData['type_id'] ?? $typeData['type']['id'];
            $type = \Craft::$app->matrix->getBlockTypeById($type_id);
            $fields = [];
            foreach ($typeData['fields'] as $fieldData) {
                if ($fieldData['id'] ?? null) {
                    $field = Themes::$plugin->fields->getById($fieldData['id']);
                    $field->populateFromData($fieldData);
                } else {
                    $field = Themes::$plugin->fields->create($fieldData);
                    $field->parent = $this;
                }
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