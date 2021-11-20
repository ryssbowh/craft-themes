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
    public static function create(?array $config = null): FieldInterface
    {
        $class = get_called_class();
        $field = new $class;
        $attributes = $field->safeAttributes();
        $config = array_intersect_key($config, array_flip($attributes));
        $field->setAttributes($config);
        if ($config['fields'] ?? null) {
            $field->fields = static::buildFields($config['fields'], $field);
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
        $fields = [];
        foreach ($craftField->columns as $order => $column) {
            $fields[] = TableField::buildConfig($column);
        }
        $config = static::buildConfig($craftField);
        $config['fields'] = $fields;
        return static::create($config);
    }

    /**
     * @inheritDoc
     */
    public static function save(FieldInterface $field): bool
    {
        foreach ($field->fields as $tableField) {
            $tableField->table = $field;
            Themes::$plugin->fields->save($tableField);
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
    public function onCraftFieldChanged(BaseField $craftField): bool
    {
        $oldFields = $this->fields;
        $newFields = [];
        $fieldIdsToKeep = [];
        $order = 0;
        foreach ($craftField->columns as $column) {
            $newConfig = TableField::buildConfig($column);
            if (isset($oldFields[$order])) {
                $oldField = $oldFields[$order];
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
                    //Column is the same, replacing name and handle in case they have changed
                    $field = $oldField;
                    $field->name = $column['heading'];
                    $field->handle = $column['handle'];
                }
                $fieldIdsToKeep[] = $field->id;
            } else {
                //New column was added to the table
                $field = TableField::create($newConfig);
            }
            $field->table = $this;
            $newFields[$order] = $field;
            $order++;
        }
        $this->fields = $newFields;
        //Deleting all fields apart from those that haven't changed to make sure project config is synced
        //New fields will be created later when the table is saved
        $oldRecords = TablePivotRecord::find()
            ->where(['table_id' => $this->id])
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
    public function populateFromPost(array $data)
    {
        $attributes = $this->safeAttributes();
        $data = array_intersect_key($data, array_flip($attributes));
        $data['fields'] = $this->buildFields($data['fields'], $this);
        $this->setAttributes($data);
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
    public function fields()
    {
        return array_merge(parent::fields(), ['fields']);
    }

    /**
     * Build table fields from an array of data
     * 
     * @param  array $data
     * @param  Table $table
     * @return array
     */
    protected static function buildFields(array $data, Table $table): array
    {
        $fields = [];
        foreach ($data as $fieldData) {
            $field = Themes::$plugin->fields->create($fieldData);
            $field->handle = $fieldData['handle'];
            $field->name = $fieldData['name'];
            $field->table = $table;
            if (!isset($fieldData['options']) and $field->displayer) {
                $field->options = $field->displayer->options->toArray();
            }
            $fields[] = $field;
        }
        return $fields;
    }
}