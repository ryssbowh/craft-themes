<?php 

namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\events\RegisterFieldsEvent;
use Ryssbowh\CraftThemes\exceptions\FieldException;
use Ryssbowh\CraftThemes\interfaces\FieldInterface;
use Ryssbowh\CraftThemes\models\Field;
use Ryssbowh\CraftThemes\models\fields\CraftField;
use Ryssbowh\CraftThemes\records\DisplayRecord;
use Ryssbowh\CraftThemes\records\FieldRecord;
use craft\base\Field as BaseField;
use craft\db\ActiveRecord;
use craft\events\ConfigEvent;
use craft\events\RebuildConfigEvent;
use craft\helpers\StringHelper;

class FieldsService extends Service
{
    const CONFIG_KEY = 'themes.fields';
    const REGISTER_FIELDS = 'registerFields';

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
     * Saves a field
     * 
     * @param  FieldInterface $field
     * @param  bool           $validate
     * @return bool
     */
    public function save(FieldInterface $field, bool $validate = true): bool
    {
        if ($validate and !$field->validate()) {
            return false;
        }

        $isNew = !is_int($field->id);
        $uid = $field->uid;

        $projectConfig = \Craft::$app->getProjectConfig();
        $configData = $field->getConfig();
        $configPath = self::CONFIG_KEY . '.' . $uid;
        $projectConfig->set($configPath, $configData);

        $record = $this->getRecordByUid($uid);
        $field->setAttributes($record->getAttributes());
        
        if ($isNew) {
            $this->add($field);
        }

        return true;
    }

    /**
     * Deletes a field
     * 
     * @param  FieldInterface $field
     * @return bool
     */
    public function delete(FieldInterface $field): bool
    {
        \Craft::$app->getProjectConfig()->remove(self::CONFIG_KEY . '.' . $field->uid);

        $this->_fields = $this->all()->where('id', '!=', $field->id);

        return true;
    }

    /**
     * Handles a change in field config
     * 
     * @param ConfigEvent $event
     */
    public function handleChanged(ConfigEvent $event)
    {
        $uid = $event->tokenMatches[0];
        $data = $event->newValue;
        $transaction = \Craft::$app->getDb()->beginTransaction();
        try {
            $data['uid'] = $uid;
            $data['display_id'] = Themes::$plugin->displays->getByUid($data['display_id'])->id;
            $this->getFieldClassByType($data['type'])::save($data);
            
            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * Handles a deletion in field config
     * 
     * @param ConfigEvent $event
     */
    public function handleDeleted(ConfigEvent $event)
    {
        $data = $event->oldValue;

        $this->getFieldClassByType($data['type'])::delete($data);
    }

    /**
     * Respond to rebuild config event
     * 
     * @param RebuildConfigEvent $e
     */
    public function rebuildConfig(RebuildConfigEvent $e)
    {
        foreach ($this->all() as $field) {
            $e->config[self::CONFIG_KEY.'.'.$field->uid] = $field->getConfig();
        }
    }

    /**
     * Create a field from a craft field
     * 
     * @param  BaseField $field
     * @return Field
     */
    public function createFromField(BaseField $craftField): Field
    {
        foreach ($this->registeredFields as $fieldClass) {
            if ($fieldClass::forField() == get_class($craftField)) {
                return $fieldClass::createFromField($craftField);
            }
        }
        return CraftField::createFromField($craftField);
    }

    /**
     * Get a field for a display
     * 
     * @param  DisplayInterface $display
     * @return ?Field
     */
    public function getForDisplay(DisplayInterface $display): ?Field
    {
        return $this->all()->firstWhere('display_id', $display->id);
    }

    /**
     * get a record by uid
     * 
     * @param  string $uid
     * @return FieldRecord
     */
    public function getRecordByUid(string $uid): FieldRecord
    {
        return FieldRecord::findOne(['uid' => $uid]) ?? new FieldRecord(['uid' => $uid]);
    }

    /**
     * Add a field to internal cache
     * 
     * @param FieldInterface $layout
     */
    protected function add(FieldInterface $field)
    {
        if (!$this->all()->firstWhere('id', $field->id)) {
            $this->all()->push($field);
        }
    }

    /**
     * Registers fields
     */
    protected function register()
    {
        $event = new RegisterFieldsEvent;
        $this->triggerEvent(self::REGISTER_FIELDS, $event);
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