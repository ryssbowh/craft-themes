<?php 

namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\FieldInterface;
use craft\base\Field as BaseField;
use craft\fields\Table as CraftTable;

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
            $field->fields = static::buildFields($config['fields']);
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
    public static function save(array $data): bool
    {
        $table = Themes::$plugin->fields->getRecordByUid($data['uid']);
        $craftField = \Craft::$app->fields->getFieldByUid($data['craft_field_id']);
        $data['craft_field_id'] = $craftField->id;
        $data['craft_field_class'] = get_class($craftField);
        $fields = $data['fields'] ?? [];
        unset($data['fields']);
        $table->setAttributes($data, false);
        $res = $table->save(false);
        foreach ($fields as $order => $fieldData) {
            $field = Themes::$plugin->fields->getRecordByUid($fieldData['uid']);
            $field->setAttributes($fieldData, false);
            $field->save(false);
            $pivot = Themes::$plugin->tables->getTablePivotRecord($table->id, $field->id);
            $pivot->field_id = $field->id;
            $pivot->table_id = $table->id;
            $pivot->order = $order;
            $pivot->name = $fieldData['name'];
            $pivot->handle = $fieldData['handle'];
            $pivot->save(false);
        }
        return $res;
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): array
    {
        $config = parent::getConfig();
        $config['fields'] = array_map(function ($field) {
            return $field->getConfig();
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
     * @param  array  $data
     * @return array
     */
    protected static function buildFields(array $data): array
    {
        $fields = [];
        foreach ($data as $fieldData) {
            $field = Themes::$plugin->fields->create($fieldData);
            $field->handle = $fieldData['handle'];
            $field->name = $fieldData['name'];
            if (!isset($fieldData['options']) and $field->displayer) {
                $field->options = $field->displayer->options->toArray();
            }
            $fields[] = $field;
        }
        return $fields;
    }
}