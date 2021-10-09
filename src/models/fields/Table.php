<?php 

namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
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
    public function onCraftFieldChanged(BaseField $craftField): bool
    {
        $oldFields = $this->fields;
        $newFields = [];
        $order = 0;
        foreach ($craftField->columns as $column) {
            $newConfig = TableField::buildConfig($column);
            if (isset($oldFields[$order])) {
                $oldField = $oldFields[$order];
                if ($oldField->craft_field_class != $newConfig['craft_field_class']) {
                    //Column has changed field class, creating a new field 
                    //and copying old fields attributes
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
            } else {
                //New column was added to the table
                $field = TableField::create($newConfig);
            }
            $field->table = $this;
            $newFields[$order] = $field;
            $order++;
        }
        $this->fields = $newFields;
        return true;
    }

    /**
     * @inheritDoc
     */
    public static function save(string $uid, array $data): bool
    {
        $table = Themes::$plugin->fields->getRecordByUid($uid);
        $craftField = \Craft::$app->fields->getFieldByUid($data['craft_field_id']);
        $data['craft_field_id'] = $craftField->id;
        $data['craft_field_class'] = get_class($craftField);
        $fields = $data['fields'] ?? [];
        unset($data['fields']);
        $table->setAttributes($data, false);
        $res = $table->save(false);
        $pivotIds = [];
        foreach ($fields as $order => $fieldData) {
            $field = Themes::$plugin->fields->getRecordByUid($fieldData['fieldUid']);
            unset($fieldData['fieldUid']);
            $field->setAttributes($fieldData, false);
            $field->save(false);
            $pivot = Themes::$plugin->tables->getTablePivotRecord($table->id, $field->id);
            $pivot->field_id = $field->id;
            $pivot->table_id = $table->id;
            $pivot->order = $order;
            $pivot->name = $fieldData['name'];
            $pivot->handle = $fieldData['handle'];
            $pivot->save(false);
            $pivotIds[] = $pivot->id;
        }
        //deleting old field records
        $oldRecords = TablePivotRecord::find()
            ->where(['table_id' => $table->id])
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
        $fieldUids = array_map(function ($field) {
            return $field['fieldUid'];
        }, $data['fields'] ?? []);
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
            $config = $field->getConfig();
            $config['fieldUid'] = $field->uid ?? StringHelper::UUID();
            return $config;
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