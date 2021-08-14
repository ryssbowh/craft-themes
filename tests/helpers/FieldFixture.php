<?php

namespace Ryssbowh\CraftThemesTests\helpers;

use craft\base\Field;
use craft\test\DbFixtureTrait;
use yii\test\DbFixture;
use yii\test\FileFixtureTrait;

abstract class FieldFixture extends DbFixture
{
    use FileFixtureTrait;
    use DbFixtureTrait;

    private $_fields = [];

    public function load()
    {
        codecept_debug('Loading fields');
        foreach ($this->loadData($this->dataFile) as $key => $data) {
            $field = $this->createField($data);

            if (!$this->saveField($field)) {
                throw new \Exception(implode(' ', $field->getErrorSummary(true)));
            }

            $this->_fields[$key] = $field;
        }
    }

    public function unload()
    {
        codecept_debug('Unloading fields');
        foreach ($this->_fields as $key => $field) {
            $this->deleteField($key);
        }
        $this->hardDelete();
    }

    public function deleteField($key)
    {
        if (isset($this->_fields[$key])) {
            $field = $this->_fields[$key];
            unset($this->_fields[$key]);
            return \Craft::$app->fields->deleteField($field);
        }
    }

    public function getField($key): ?Field
    {
        return $this->_fields[$key] ?? null;
    }

    protected function createField(array $data): Field
    {
        $class = $data['type'];
        return new $class($data['data']);
    }

    protected function saveField(Field $field)
    {
        return \Craft::$app->fields->saveField($field);
    }
}