<?php 

namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\events\RegisterFieldsEvent;
use Ryssbowh\CraftThemes\exceptions\FieldException;
use Ryssbowh\CraftThemes\models\fields\Field;
use Ryssbowh\CraftThemes\records\DisplayRecord;
use Ryssbowh\CraftThemes\records\FieldRecord;
use craft\db\ActiveRecord;

class FieldsService extends Service
{
    const REGISTER_FIELDS_EVENT = 'registerFields';

    /**
     * @var Collection
     */
    private $_fields;

    /**
     * List of registered fields, indexed by their types
     * @var array
     */
    private $_registered;

    /**
     * Get all fields
     * 
     * @return Collection
     */
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

    /**
     * Get all registered fields
     * 
     * @return array
     */
    public function getRegisteredFields(): array
    {
        if ($this->_registered == null) {
            $this->register();
        }
        return $this->_registered;
    }

    /**
     * Get all valid field types
     * 
     * @return array
     */
    public function getValidTypes(): array
    {
        return array_keys($this->registeredFields);
    }

    /**
     * Get a field by id
     * 
     * @param  int    $id
     * @return Field
     */
    public function getById(int $id): Field
    {
        return $this->all()->firstWhere('id', $id);
    }

    /**
     * Create a field from config
     * 
     * @param  array|ActiveRecord $config
     * @return Field
     * @throws FieldException
     */
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

    /**
     * deletes a field
     * 
     * @param  Field  $field
     */
    public function deleteField(Field $field)
    {
        $field->delete();
    }

    /**
     * Get a field for a display
     * 
     * @param  int    $displayId
     * @return ?Field
     */
    public function getForDisplay(int $displayId): ?Field
    {
        return $this->all()->firstWhere('display_id', $displayId);
    }

    /**
     * Saves a field data
     * 
     * @param  array         $data
     * @param  DisplayRecord $display
     * @return bool
     */
    public function save(array $data, DisplayRecord $display): bool
    {
        if (!isset($data['type'])) {
            throw FieldException::noType();
        }
        $data['display_id'] = $display->id;
        return $this->getFieldClassByType($data['type'])::save($data, $display);
    }

    /**
     * get a record by uid
     * 
     * @param  string $uid
     * @return FieldRecord
     */
    public function getRecordByUid(string $uid): FieldRecord
    {
        return FieldRecord::findOne(['uid' => $uid]) ?? new FieldRecord;
    }

    /**
     * Registers fields
     */
    protected function register()
    {
        $event = new RegisterFieldsEvent;
        $this->triggerEvent(self::REGISTER_FIELDS_EVENT, $event);
        $this->_registered = $event->fields;
    }

    /**
     * Get a field class for a type
     * 
     * @param  string $type
     * @return string
     */
    protected function getFieldClassByType(string $type): string
    {
        $fields = $this->registeredFields;
        if (!isset($fields[$type])) {
            throw FieldException::unknownType($type);
        }
        return $fields[$type];
    }
}