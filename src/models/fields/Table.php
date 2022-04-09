<?php
namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\helpers\ProjectConfigHelper;
use Ryssbowh\CraftThemes\interfaces\FieldInterface;
use Ryssbowh\CraftThemes\records\FieldRecord;
use Ryssbowh\CraftThemes\records\TablePivotRecord;
use craft\base\Field as BaseField;
use craft\fields\Table as CraftTable;
use craft\helpers\StringHelper;

/**
 * Handles a Craft table field
 */
class Table extends CraftField
{
    private $_fields;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            ['fields', 'safe'],
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
        foreach ($this->fields as $field) {
            if ($field->hasErrors()) {
                return true;
            }
        }
        return parent::hasErrors();
    }

    /**
     * @inheritDoc
     */
    public function afterValidate()
    {
        foreach ($this->fields as $field) {
            $field->validate();
        }
        parent::afterValidate();
    }

    /**
     * @inheritDoc
     */
    public static function getType(): string
    {
        return 'table';
    }

    /**
     * @inheritDoc
     */
    public static function forField(): string
    {
        return CraftTable::class;
    }

    /**
     * @inheritDoc
     */
    public function populateFromData(array $data)
    {
        $data = array_intersect_key($data, array_flip($this->safeAttributes()));
        if ($data['fields'] ?? null) {
            $data['fields'] = $this->buildFields($data['fields']);
        }
        $this->setAttributes($data);
    }

    /**
     * @inheritDoc
     */
    public static function buildConfig(BaseField $craftField): array
    {
        $config = parent::buildConfig($craftField);
        $fields = [];
        foreach ($craftField->columns as $column) {
            $fields[] = TableField::buildConfig($column);
        }
        $config['fields'] = $fields;
        return $config;
    }

    /**
     * @inheritDoc
     */
    public static function save(FieldInterface $field): bool
    {
        $fieldsToKeep = [];
        $children = Themes::$plugin->tables->getForTable($field);
        foreach ($field->fields as $tableField) {
            // $tableField->parent = $field;
            Themes::$plugin->fields->save($tableField);
            $fieldsToKeep[] = $tableField->id;
        }
        foreach ($children as $child) {
            if (!in_array($child->id, $fieldsToKeep)) {
                Themes::$plugin->fields->delete($child);
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
        $table = Themes::$plugin->fields->getRecordByUid($uid);
        $fields = $data['fields'] ?? [];
        ProjectConfigHelper::ensureFieldsProcessed(array_map(function ($data) {
            return $data['uid'];
        }, $fields));
        foreach ($fields as $order => $fieldData) {
            $field = Themes::$plugin->fields->getRecordByUid($fieldData['uid']);
            $pivot = Themes::$plugin->tables->getTablePivotRecord($table->id, $field->id);
            $pivot->order = $order;
            $pivot->name = $fieldData['name'];
            $pivot->handle = $fieldData['handle'];
            $pivot->save(false);
        }
    }

    /**
     * @inheritDoc
     */
    public static function delete(FieldInterface $field): bool
    {
        foreach ($field->fields as $tableField) {
            Themes::$plugin->fields->delete($tableField);
        }
        return parent::delete($field);
    }

    /**
     * @inheritDoc
     */
    public function rebuild(): bool
    {
        $oldFields = $this->fields;
        $newFields = [];
        $fieldIdsToKeep = [];
        $order = 0;
        foreach ($this->craftField->columns as $column) {
            $newConfig = TableField::buildConfig($column);
            $oldField = $this->getFieldByHandle($column['handle']);
            if ($oldField) {
                if ($oldField->craft_field_class != $newConfig['craft_field_class']) {
                    //Column has changed field class, creating a new field and copying old fields attributes
                    $field = TableField::create($newConfig);
                    $field->id = $oldField->id;
                    $field->uid = $oldField->uid;
                    $field->labelHidden = $oldField->labelHidden;
                    $field->labelVisuallyHidden = $oldField->labelVisuallyHidden;
                    $field->visuallyHidden = $oldField->visuallyHidden;
                    $field->hidden = $field->hidden ?: $oldField->hidden;
                } else {
                    //Column is the same, replacing name in case it's changed
                    $field = $oldField;
                    $field->name = $column['heading'];
                }
                $fieldIdsToKeep[] = $field->id;
            } else {
                //New column was added to the table
                $field = TableField::create($newConfig);
            }
            $field->parent = $this;
            $newFields[$order] = $field;
            $order++;
        }
        $this->fields = $newFields;
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): array
    {
        $config = parent::getConfig();
        $config['fields'] = array_map(function ($field) {
            return [
                'name' => $field->name,
                'handle' => $field->handle,
                'uid' => $field->uid
            ];
        }, $this->fields);
        return $config;
    }

    /**
     * @inheritDoc
     */
    public function getFields(): array
    {
        if ($this->_fields === null) {
            return Themes::$plugin->tables->getForTable($this);
        }
        return $this->_fields;
    }

    /**
     * @inheritDoc
     */
    public function getVisibleFields(): array
    {
        return array_filter($this->fields, function ($field) {
            return $field->isVisible();
        });
    }

    /**
     * Get a sub field by handle
     * 
     * @param  string $handle
     * @return ?FieldInterface
     */
    public function getFieldByHandle(string $handle): ?FieldInterface
    {
        foreach ($this->fields as $field) {
            if ($field->handle == $handle) {
                return $field;
            }
        }
        return null;
    }

    /**
     * Fields setter
     * 
     * @param array $fields
     */
    public function setFields(array $fields)
    {
        $this->_fields = $fields;
    }

    /**
     * @inheritDoc
     */
    public function fields(): array
    {
        return array_merge(parent::fields(), ['fields']);
    }

    /**
     * Build table fields from an array of data
     * 
     * @param  array $data
     * @return array
     */
    protected function buildFields(array $data): array
    {
        $fields = [];
        foreach ($data as $fieldData) {
            if ($fieldData['id'] ?? null) {
                $field = Themes::$plugin->fields->getById($fieldData['id']);
                $field->populateFromData($fieldData);
            } else {
                $field = Themes::$plugin->fields->create($fieldData);
                $field->parent = $this;
            }
            $field->handle = $fieldData['handle'];
            $field->name = $fieldData['name'];
            $fields[] = $field;
        }
        return $fields;
    }
}