<?php 

namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\events\RegisterFieldsEvent;
use Ryssbowh\CraftThemes\exceptions\DisplayException;
use Ryssbowh\CraftThemes\exceptions\FieldException;
use Ryssbowh\CraftThemes\models\DisplayItem;
use Ryssbowh\CraftThemes\models\DisplayMatrixType;
use Ryssbowh\CraftThemes\models\DisplayTitle;
use Ryssbowh\CraftThemes\models\fields\CraftField;
use Ryssbowh\CraftThemes\models\fields\Field;
use Ryssbowh\CraftThemes\models\fields\Matrix;
use Ryssbowh\CraftThemes\models\fields\Title;
use Ryssbowh\CraftThemes\records\DisplayRecord;
use Ryssbowh\CraftThemes\records\FieldRecord;
use Ryssbowh\CraftThemes\records\MatrixPivotRecord;
use craft\base\Field as BaseField;
use craft\db\ActiveRecord;
use craft\fields\Matrix as CraftMatrix;
use craft\models\MatrixBlockType;

class FieldsService extends Service
{
    const REGISTER_FIELDS_EVENT = 'registerFields';

    private $_fields;
    private $_registered;

    public function all()
    {
        if ($this->_fields === null) {
            $records = FieldRecord::find()->all();
            $this->_fields = collect();
            foreach ($records as $record) {
                $this->_fields->push($this->create($record));
            }
        }
        return $this->_fields;
    }

    public function getRegisteredFields(): array
    {
        if ($this->_registered == null) {
            $event = new RegisterFieldsEvent;
            $this->triggerEvent(self::REGISTER_FIELDS_EVENT, $event);
            $this->_registered = $event->fields;
        }
        return $this->_registered;
    }

    public function getFieldClassByType(string $type): string
    {
        $fields = $this->registeredFields;
        if (!isset($fields[$type])) {
            throw FieldException::unknownType($type);
        }
        return $fields[$type];
    }

    public function getValidTypes(): array
    {
        return array_keys($this->registeredFields);
    }

    public function getById(int $id): Field
    {
        return $this->all()->firstWhere('id', $id);
    }

    public function create($config): Field
    {
        if ($config instanceof ActiveRecord) {
            $config = $config->getAttributes();
        }
        if (!isset($config['type'])) {
            throw FieldException::noType();
        }
        return $this->getFieldClassByType($config['type'])::create($config);
    }

    public function deleteField(Field $field)
    {
        \Craft::$app->getDb()->createCommand()
            ->delete(FieldRecord::tableName(), ['id' => $field->id])
            ->execute();
    }

    public function getForDisplay(int $id): Field
    {
        return $this->all()->firstWhere('display_id', $id);
    }

    public function save(array $data, DisplayRecord $display): bool
    {
        if (!isset($data['type'])) {
            throw FieldException::noType();
        }
        $data['display_id'] = $display->id;
        return $this->getFieldClassByType($data['type'])::save($data, $display);
    }

    public function getRecordByUid(string $uid): FieldRecord
    {
        return FieldRecord::findOne(['uid' => $uid]) ?? new FieldRecord;
    }
}