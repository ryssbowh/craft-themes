<?php
namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\events\RegisterFieldsEvent;
use Ryssbowh\CraftThemes\exceptions\FieldException;
use Ryssbowh\CraftThemes\helpers\ProjectConfigHelper;
use Ryssbowh\CraftThemes\interfaces\DisplayInterface;
use Ryssbowh\CraftThemes\interfaces\FieldInterface;
use Ryssbowh\CraftThemes\models\Field;
use Ryssbowh\CraftThemes\models\fields\CraftField;
use Ryssbowh\CraftThemes\records\DisplayRecord;
use Ryssbowh\CraftThemes\records\FieldRecord;
use craft\base\Field as BaseField;
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
        if ($config instanceof FieldRecord) {
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

        if ($field->displayer and !$field->displayer->options->validate()) {
            return false;
        }
        
        $isNew = !is_int($field->id);

        if (!$field::save($field)) {
            return false;
        }
        
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
        if ($field::delete($field)) {
            $this->_fields = $this->all()->where('id', '!=', $field->id);
            return true;
        }
        return false;
    }

    /**
     * Handles a change in field config
     * 
     * @param ConfigEvent $event
     */
    public function handleChanged(ConfigEvent $event)
    {
        ProjectConfigHelper::ensureAllDisplaysProcessed();
        $uid = $event->tokenMatches[0];
        $data = $event->newValue;
        if (!$data) {
            //This can happen when fixing broken states
            return;
        }
        $transaction = \Craft::$app->getDb()->beginTransaction();
        try {
            if (isset($data['display_id'])) {
                $display = Themes::$plugin->displays->getRecordByUid($data['display_id']);
                $data['display_id'] = Themes::$plugin->displays->getRecordByUid($data['display_id'])->id;
            }
            //Forward to each type of field :
            $this->getFieldClassByType($data['type'])::handleChanged($uid, $data);
            
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
        $uid = $event->tokenMatches[0];
        $this->getFieldClassByType($data['type'])::handleDeleted($uid, $data);
    }

    /**
     * Respond to rebuild config event
     * 
     * @param RebuildConfigEvent $e
     */
    public function rebuildConfig(RebuildConfigEvent $e)
    {
        $parts = explode('.', self::CONFIG_KEY);
        foreach ($this->all() as $field) {
            $e->config[$parts[0]][$parts[1]][$field->uid] = $field->getConfig();
        }
    }

    /**
     * Populates a field from post
     * 
     * @param  array            $data
     * @param  DisplayInterface $display
     * @return FieldInterface
     */
    public function populateFromPost(array $data, DisplayInterface $display): FieldInterface
    {
        $field = $this->getById($data['id']);
        $field->populateFromPost($data);
        return $field;
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