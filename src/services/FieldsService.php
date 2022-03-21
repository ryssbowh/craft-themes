<?php
namespace Ryssbowh\CraftThemes\services;

use Illuminate\Support\Collection;
use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\events\RegisterFieldsEvent;
use Ryssbowh\CraftThemes\exceptions\FieldException;
use Ryssbowh\CraftThemes\helpers\ProjectConfigHelper;
use Ryssbowh\CraftThemes\interfaces\DisplayInterface;
use Ryssbowh\CraftThemes\interfaces\FieldInterface;
use Ryssbowh\CraftThemes\models\fields\CraftField;
use Ryssbowh\CraftThemes\records\DisplayRecord;
use Ryssbowh\CraftThemes\records\FieldRecord;
use Ryssbowh\CraftThemes\records\ParentPivotRecord;
use craft\base\Field;
use craft\events\ConfigEvent;
use craft\events\FieldEvent;
use craft\events\RebuildConfigEvent;
use craft\helpers\StringHelper;
use yii\caching\TagDependency;

class FieldsService extends Service
{
    const CONFIG_KEY = 'themes.fields';
    const EVENT_REGISTER_FIELDS = 'registerFields';

    /**
     * @var Collection
     */
    private $_fields;

    /**
     * @var Collection
     */
    private $_parentPivots;

    /**
     * List of registered fields, indexed by their types
     * @var string[]
     */
    private $_registered;

    /**
     * Get all fields
     * 
     * @return Collection
     */
    public function getAll(): Collection
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
     * Get all parent pivots
     * 
     * @return Collection
     */
    public function getParentPivots(): Collection
    {
        if ($this->_parentPivots === null) {
            $this->_parentPivots = collect(ParentPivotRecord::find()
                ->with(['field', 'parent'])
                ->orderBy(['order' => SORT_ASC])
                ->all()
            );
        }
        return $this->_parentPivots;
    }

    /**
     * Get the child of a field related to a Craft field
     * 
     * @param  FieldInterface $field
     * @param  Field          $craftField
     * @return ?FieldInterface
     */
    public function getChild(FieldInterface $field, Field $craftField): ?FieldInterface
    {
        $child = $this->parentPivots
            ->where('parent_id', $field->id)
            ->where('field.craft_field_id', $craftField->id)
            ->first();
        if ($child) {
            $child = $this->getById($child->field_id);
        }
        return $child;
    }

    /**
     * Get the parent of a field
     * 
     * @param  FieldInterface $field
     * @return ?FieldInterface
     */
    public function getParent(FieldInterface $field): ?FieldInterface
    {
        if ($field->id) {
            $pivot = $this->parentPivots
                ->firstWhere('field_id', $field->id);
            return $pivot ? $this->getById($pivot->parent_id) : null;
        }
        return null;
    }

    /**
     * Get the children pivots for a parent
     * 
     * @param  int $parentId
     * @return ParentPivotRecord[]
     */
    public function getChildrenPivots(int $parentId): array
    {
        return $this->parentPivots
            ->where('parent_id', $parentId)
            ->values()
            ->all();
    }

    /**
     * Get the children of a field
     * 
     * @param  FieldInterface $field
     * @return FieldInterface[]
     */
    public function getChildren(FieldInterface $field): array
    {
        if (!$field->id) {
            return [];
        }
        $_this = $this;
        return array_map(function ($record) use ($_this, $field) {
            return $_this->getById($record->field_id);
        }, $this->getChildrenPivots($field->id));
    }

    /**
     * Get a parent pivot record, or creates a new one
     * 
     * @param  int    $parentId
     * @param  int    $fieldId
     * @return ParentPivotRecord
     */
    public function getParentPivotRecord(int $parentId, int $fieldId): ParentPivotRecord
    {
        return $this->parentPivots
            ->where('parent_id', $parentId)
            ->firstWhere('field_id', $fieldId)
            ?? new ParentPivotRecord([
                'parent_id' => $parentId,
                'field_id' => $fieldId
            ]);
    }

    /**
     * Get all fields for a craft field id
     * 
     * @param  int    $fieldId
     * @return FieldInterface[]
     */
    public function getAllForCraftField(int $fieldId): array
    {
        return $this->getAll()
            ->where('craft_field_id', $fieldId)
            ->values()
            ->all();
    }

    /**
     * Get the type of field for a craft field
     * 
     * @param  Field $field
     * @return string
     */
    public function getTypeForCraftField(Field $field): string
    {
        foreach ($this->registeredFields as $type => $class) {
            if ($class::forField() == get_class($field)) {
                return $type;
            }
        }
        return 'field';
    }

    /**
     * Get a field class for a type
     * 
     * @param  string $type
     * @return string
     */
    public function getFieldClassByType(string $type): string
    { 
        $fields = $this->registeredFields;
        if (!isset($fields[$type])) {
            throw FieldException::unknownType($type);
        }
        return $fields[$type];
    }

    /**
     * Handles a craft field save: If the type of field has changed we#ll delete the field and recreate it
     * Otherwise we'll rebuild it in case changes are to be made by the field
     * 
     * @param FieldEvent $event
     */
    public function onCraftFieldSaved(FieldEvent $event)
    {
        if ($event->isNew) {
            return;
        }
        $craftField = $event->field;
        $fields = $this->getAllForCraftField($craftField->id);
        foreach ($fields as $field) {
            $display = $field->display;
            if ($field->craft_field_class != get_class($craftField)) {
                // Field has changed class, deleting old field, recreating it
                // and copying old field attributes
                $this->delete($field);
                $newField = $this->fieldsService()->createFromField($craftField);
                $newField->setAttributes([
                    'labelHidden' => $field->labelHidden,
                    'visuallyHidden' => $field->labelVisuallyHidden,
                    'labelVisuallyHidden' => $field->labelVisuallyHidden,
                    'hidden' => $newField->hidden ?: $field->hidden,
                    'display' => $display
                ]);
                $this->save($newField);
            } else {
                $field->rebuild();
                $this->save($field);
            }
        }
    }

    /**
     * Get all registered fields
     * 
     * @return string[]
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
     * @return string[]
     */
    public function getValidTypes(): array
    {
        return array_keys($this->registeredFields);
    }

    /**
     * Get a field by id
     * 
     * @param  int    $id
     * @return FieldInterface
     */
    public function getById(int $id): FieldInterface
    {
        return $this->resolveParent($this->getAll()->firstWhere('id', $id));
    }

    /**
     * Create a field from config or active record
     * 
     * @param  array|ActiveRecord $config
     * @return FieldInterface
     * @throws FieldException
     */
    public function create($config): FieldInterface
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
     * Create a field from a craft field
     * 
     * @param  Field $field
     * @return FieldInterface
     */
    public function createFromField(Field $craftField): FieldInterface
    {
        return $this->create($this->buildConfig($craftField));
    }

    /**
     * Build the config for a craft field
     * 
     * @param  Field $field
     * @return array
     */
    public function buildConfig(Field $craftField): array
    {
        foreach ($this->registeredFields as $fieldClass) {
            if ($fieldClass::forField() == get_class($craftField)) {
                return $fieldClass::buildConfig($craftField);
            }
        }
        return CraftField::buildConfig($craftField);
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

        TagDependency::invalidate(\Craft::$app->cache, DisplayerCacheService::DISPLAYER_CACHE_TAG . '::' . $field->id);
        $this->_parentPivots = null;

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
            $this->_fields = $this->getAll()->where('id', '!=', $field->id);
            $this->_parentPivots = null;
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
        foreach ($this->getAll() as $field) {
            $e->config[$parts[0]][$parts[1]][$field->uid] = $field->getConfig();
        }
    }

    /**
     * Populates a field from array of data
     * 
     * @param  array            $data
     * @return FieldInterface
     */
    public function populateFromData(array $data): FieldInterface
    {
        $field = $this->getById($data['id']);
        $field->populateFromData($data);
        return $field;
    }

    /**
     * Get a field for a display
     * 
     * @param  DisplayInterface $display
     * @return ?FieldInterface
     */
    public function getForDisplay(DisplayInterface $display): ?FieldInterface
    {
        return $this->resolveParent($this->getAll()->firstWhere('display_id', $display->id));
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
        if (!$this->getAll()->firstWhere('id', $field->id)) {
            $this->getAll()->push($field);
        }
    }

    /**
     * Registers fields
     */
    protected function register()
    {
        $event = new RegisterFieldsEvent;
        $this->triggerEvent(self::EVENT_REGISTER_FIELDS, $event);
        $this->_registered = $event->fields;
    }

    /**
     * Resolve the parent of a field
     * 
     * @param  FieldInterface $field
     * @return ?FieldInterface
     */
    protected function resolveParent(?FieldInterface $field): ?FieldInterface
    {
        if ($field) {
            $field->parent = $this->getParent($field);
        }
        return $field;
    }
}