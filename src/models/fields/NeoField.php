<?php
namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\helpers\ProjectConfigHelper;
use Ryssbowh\CraftThemes\interfaces\FieldInterface;
use Ryssbowh\CraftThemes\models\DisplayNeoType;
use Ryssbowh\CraftThemes\services\DisplayerCacheService;
use benf\neo\Field as BaseNeoField;
use benf\neo\Plugin;
use benf\neo\elements\Block;
use craft\base\Field as BaseField;
use craft\fieldlayoutelements\CustomField;
use craft\helpers\StringHelper;

/**
 * Handles a Neo field
 *
 * @since 3.2.0
 */
class NeoField extends CraftField
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
    public function hasErrors($attribute = null): bool
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
    public function getChildFieldName(FieldInterface $field): string
    {
        //Search for the block type that contains this field
        $typeWithField = null;
        foreach ($this->types as $type) {
            foreach ($type->fields as $field2) {
                if ($field->id == $field2->id) {
                    $typeWithField = $type->type;
                    break 2;
                }
            }
        }
        //Search in the block type tabs for a custom name for that field
        if ($typeWithField) {
            foreach ($typeWithField->fieldLayout->tabs as $tab) {
                foreach ($tab->elements as $element) {
                    if (get_class($element) == CustomField::class and $element->field->id == $field->craftField->id) {
                        return $element->label ?: $element->field->name;
                    }
                }
            }
        }
        return $field->craftField->name;
    }

    /**
     * @inheritDoc
     */
    public static function getType(): string
    {
        return 'neo';
    }

    /**
     * @inheritDoc
     */
    public static function forField(): string
    {
        return BaseNeoField::class;
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
    public function rebuild()
    {
        $oldTypes = $this->types;
        $newTypes = [];
        $fieldIdsToKeep = [];
        foreach ($this->craftField->getBlockTypes() as $blockType) {
            if (isset($oldTypes[$blockType->handle])) {
                $type = $oldTypes[$blockType->handle];
            } else {
                //Type doesn't exist on the Neo, creating a new one
                $type = new DisplayNeoType([
                    'type' => $blockType,
                    'fields' => []
                ]);
            }
            $fields = [];
            $fieldLayout = $blockType->getFieldLayout();
            foreach ($fieldLayout->customFields as $craftField) {
                try {
                    $oldField = $type->getFieldById($craftField->id);
                } catch (\Throwable $e) {
                    $oldField = null;
                }
                if (!$oldField) {
                    //New field was added to the block type, creating new field
                    $field = Themes::$plugin->fields->createFromField($craftField);
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
                } else {
                    //Field hasn't changed but forwarding the change to sub fields, in case they have things to change
                    $field = $oldField;
                    $field->rebuild();
                    $fieldIdsToKeep[] = $field->id;
                }
                $field->parent = $this;
                $fields[] = $field;
            }
            $type->fields = $fields;
            $newTypes[$blockType->handle] = $type;
        }
        $this->types = $newTypes;
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
                }, $type->getCustomFields())
            ];
        }
        $config['types'] = $types;
        return $config;
    }

    /**
     * @inheritDoc
     */
    public function populateFromData(array $data)
    {
        $attributes = $this->safeAttributes();
        $data = array_intersect_key($data, array_flip($attributes));
        if ($data['types'] ?? null) {
            $data['types'] = $this->buildNeoTypes($data['types'], $this);
        }
        $this->setAttributes($data);
    }

    /**
     * @inheritDoc
     */
    public function getChildren(): array
    {
        $children = [];
        foreach ($this->types as $type) {
            foreach ($type->fields as $field) {
                $children[] = $field;
                $children = array_merge($children, $field->children);
            }
        }
        return $children;
    }

    /**
     * @inheritDoc
     */
    public static function handleChanged(string $uid, array $data)
    {
        parent::handleChanged($uid, $data);
        $neo = Themes::$plugin->fields->getRecordByUid($uid);
        foreach ($data['types'] ?? [] as $typeData) {
            $fields = $typeData['fields'] ?? [];
            ProjectConfigHelper::ensureFieldsProcessed($fields);
            foreach ($fields as $order => $fieldUid) {
                $field = Themes::$plugin->fields->getRecordByUid($fieldUid);
                $pivot = Themes::$plugin->fields->getParentPivotRecord($neo->id, $field->id);
                $pivot->order = $order;
                $pivot->data = [
                    'type_uid' => $typeData['type_uid']
                ];
                $pivot->save(false);
            }
        }
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
            $children = Themes::$plugin->fields->getChildrenPivots($this->id);
            foreach ($this->craftField->getBlockTypes() as $type) {
                $fieldIds = array_map(function ($field) {
                    return $field->id;
                }, $type->customFields);
                $pivots = array_filter($children, function ($pivot) use ($type, $fieldIds) {
                    if (($pivot->decodedData['type_uid'] ?? '') != $type->uid or !in_array($pivot->field->craft_field_id, $fieldIds)) {
                        return false;
                    }
                    return true;
                });
                $fields = array_map(function ($pivot) {
                    return Themes::$plugin->fields->getById($pivot->field_id);
                }, $pivots);
                $this->_types[$type->handle] = new DisplayNeoType([
                    'type' => $type,
                    'fields' => array_values($fields)
                ]);
            }
        }
        return $this->_types;
    }

    /**
     * @inheritDoc
     */
    public function getVisibleFields(Block $block): array
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
    public function setTypes(array $types)
    {
        $this->_types = $types;
    }

    /**
     * @inheritDoc
     */
    public function fields(): array
    {
        return array_merge(parent::fields(), ['types']);
    }

    /**
     * Build neo block types from an array of data
     * 
     * @param  array    $data
     * @param  NeoField $neoField
     * @return array
     */
    protected static function buildNeoTypes(array $data, NeoField $neoField): array
    {
        $types = [];
        foreach ($data as $typeData) {
            $type_id = $typeData['type_id'] ?? $typeData['type']['id'];
            $type = Plugin::$plugin->blockTypes->getById($type_id);
            $fields = [];
            foreach ($typeData['fields'] as $fieldData) {
                if ($fieldData['id'] ?? null) {
                    $field = Themes::$plugin->fields->getById($fieldData['id']);
                    $field->populateFromData($fieldData);
                } else {
                    $field = Themes::$plugin->fields->create($fieldData);
                    $field->parent = $neoField;
                }
                if (!isset($fieldData['options']) and $field->displayer) {
                    $field->options = $field->displayer->options->toArray();
                }
                $fields[] = $field;
            }
            $types[$type->handle] = new DisplayNeoType([
                'type' => $type,
                'fields' => $fields
            ]);
        }
        return $types;
    }
}