<?php 

namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\events\FieldDisplayerEvent;
use Ryssbowh\CraftThemes\events\FieldEvent;
use Ryssbowh\CraftThemes\exceptions\FieldDisplayerException;
use Ryssbowh\CraftThemes\exceptions\FieldException;
use Ryssbowh\CraftThemes\interfaces\FieldDisplayerInterface;
use Ryssbowh\CraftThemes\models\Field;
use Ryssbowh\CraftThemes\models\ViewMode;
use Ryssbowh\CraftThemes\models\layouts\Layout;
use Ryssbowh\CraftThemes\records\FieldRecord;
use craft\base\Field as CraftField;
use craft\events\ConfigEvent;
use craft\fieldlayoutelements\TitleField;
use craft\helpers\StringHelper;

class FieldsService extends Service
{
    const REGISTER_DISPLAYERS = 1;

    const EVENT_BEFORE_SAVE = 1;
    const EVENT_AFTER_SAVE = 2;
    const EVENT_BEFORE_APPLY_DELETE = 3;
    const EVENT_AFTER_DELETE = 4;
    const EVENT_BEFORE_DELETE = 5;
    const CONFIG_KEY = 'themes.fields';

    protected $_defaults;
    protected $_displayers;
    protected $_mapping;

    public function getById(int $id)
    {
        $record = FieldRecord::find()->where(['id' => $id])->one();
        if ($record) {
            return $record->toModel();
        }
        throw FieldException::noId($id);
    }

    public function getForLayoutAndViewMode(Layout $layout, ViewMode $viewMode): array
    {
        $fields = FieldRecord::find()->where([
            'layout' => $layout->id,
            'viewMode' => $viewMode->id
        ])->all();
        return array_map(function ($record) {
            return $record->toModel();
        }, $fields);
    }

    public function getForLayout(Layout $layout): array
    {
        $fields = FieldRecord::find()->where([
            'layout' => $layout->id
        ])->all();
        return array_map(function ($record) {
            return $record->toModel();
        }, $fields);
    }

    public function getDefaults(): array
    {
        if (is_null($this->_defaults)) {
            $this->registerDisplayers();
        }
        return $this->_defaults;
    }

    public function getMapping(): array
    {
        if (is_null($this->_mapping)) {
            $this->registerDisplayers();
        }
        return $this->_mapping;
    }

    public function getDisplayerByHandle(string $handle): FieldDisplayerInterface
    {
        if (!isset($this->getAllDisplayers()[$handle])) {
            throw FieldDisplayerException::displayerNotDefined($handle);
        }
        return $this->getAllDisplayers()[$handle];
    }

    public function getDisplayersByHandle(array $handles): array
    {
        $_this = $this;
        return array_map(function ($handle) use ($_this) {
            return $_this->getDisplayerByHandle($handle);
        }, $handles);
    }

    public function getAllDisplayers(): array
    {
        if (is_null($this->_displayers)) {
            $this->registerDisplayers();
        }
        return $this->_displayers;
    }

    public function getDisplayers(string $fieldClass): array
    {
        return $this->getDisplayersByHandle($this->getMapping()[$fieldClass] ?? []);
    }

    public function getDefaultDisplayer(string $fieldClass): ?FieldDisplayerInterface
    {
        if (!$default = $this->getDefaults()[$fieldClass] ?? false) {
            if (!$default = $this->getDisplayers($fieldClass)[0] ?? false) {
                return null;
            }
        }
        return $this->getDisplayerByHandle($default);
    }

    /**
     * Delete all fields which id is not in $toKeep for a layout
     * 
     * @param array $toKeep
     * @param int   $layoutId
     */
    public function deleteForLayout(array $toKeep, int $layoutId)
    {
        $fields = FieldRecord::find()->where([
            'layout' => $layoutId
        ])->andWhere(['not in', 'id', $toKeep])->all();
        foreach ($fields as $field) {
            $this->delete($field);
        }
    }

    /**
     * Get a field from raw data
     * 
     * @param  array  $data
     * @return Field
     */
    public function fromData(array $data): Field
    {
        unset($data['availableDisplayers']);
        unset($data['uid']);
        unset($data['name']);
        unset($data['type']);
        if (isset($data['id'])) {
            $field = $this->getById($data['id']);
            $field->setAttributes($data);
        } else {
            $field = new Field($data);
        }
        return $field;
    }

    /**
     * Saves one field
     * 
     * @param  Field $field
     * @return bool
     */
    public function save(Field $field, bool $validate = false): bool
    {
        if (!$field->validate()) {
            return false;
        }
        
        $isNew = !is_int($field->id);
        $uid = $isNew ? StringHelper::UUID() : $field->uid;

        $this->triggerEvent(self::EVENT_BEFORE_SAVE, new FieldEvent([
            'field' => $field
        ]));

        $projectConfig = \Craft::$app->getProjectConfig();
        $configData = $field->getConfig();
        $configPath = self::CONFIG_KEY . '.' . $uid;
        $projectConfig->set($configPath, $configData);

        $record = $this->getRecordByUid($uid);
        $record->save(false);
        
        if ($isNew) {
            $field->setAttributes($record->getAttributes(), false);
        }

        return true;
    }

    /**
     * Deletes one field
     * 
     * @param  Field $record
     * @return bool
     */
    public function deleteBlock(Field $record): bool
    {
        $this->triggerEvent(self::EVENT_BEFORE_DELETE, new FieldEvent([
            'field' => $record
        ]));
        \Craft::$app->getProjectConfig()->remove(self::CONFIG_KEY . '.' . $record->uid);
        return true;
    }

    /**
     * Handles field config change
     * 
     * @param ConfigEvent $event
     */
    public function handleChanged(ConfigEvent $event)
    {
        $uid = $event->tokenMatches[0];
        $data = $event->newValue;
        $transaction = \Craft::$app->getDb()->beginTransaction();

        try {
            $field = $this->getRecordByUid($uid);
            $isNew = $field->getIsNewRecord();

            $field->uid = $uid;
            $field->field = $data['field'];
            $field->layout = $this->layoutService()->getRecordByUid($data['layout'])->id;
            $field->viewMode = $this->viewModeService()->getRecordByUid($data['viewMode'])->id;
            $field->displayerHandle = $data['displayerHandle'];
            $field->labelHidden = $data['labelHidden'];
            $field->order = $data['order'];
            $field->hidden = $data['hidden'];
            $field->visuallyHidden = $data['visuallyHidden'];
            $field->labelVisuallyHidden = $data['labelVisuallyHidden'];
            $field->options = $data['options'] ?? [];
            
            $field->save(false);
            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }

        $this->triggerEvent(self::EVENT_AFTER_SAVE, new FieldEvent([
            'field' => $field,
            'isNew' => $isNew,
        ]));
    }

    /**
     * Hanles field config deletion
     * 
     * @param ConfigEvent $event
     */
    public function handleDeleted(ConfigEvent $event)
    {
        $uid = $event->tokenMatches[0];
        $field = $this->getRecordByUid($uid);

        if (!$field) {
            return;
        }

        $this->triggerEvent(self::EVENT_BEFORE_APPLY_DELETE, new FieldEvent([
            'field' => $field
        ]));

        \Craft::$app->getDb()->createCommand()
            ->delete(BlockRecord::tableName(), ['uid' => $uid])
            ->execute();

        $this->triggerEvent(self::EVENT_AFTER_DELETE, new FieldEvent([
            'field' => $field
        ]));
    }

    /**
     * Get field record by uid or a new one if not found
     * 
     * @param  string $uid
     * @return FieldRecord
     */
    public function getRecordByUid(string $uid): FieldRecord
    {
        return FieldRecord::findOne(['uid' => $uid]) ?? new FieldRecord;
    }

    protected function registerDisplayers()
    {
        if ($this->_defaults !== null) {
            return;
        }
        $event = new FieldDisplayerEvent;
        $this->triggerEvent(self::REGISTER_DISPLAYERS, $event);
        $this->_defaults = $event->getDefaults();
        $this->_displayers = $event->getDisplayers();
        $this->_mapping = $event->getMapping();
    }

    protected function createFields(Layout $layout, ViewMode $viewMode): array
    {
        $fieldLayout = $layout->element()->getFieldLayout();
        $fields = [$this->createTitleField($layout, $viewMode)];
        foreach ($fieldLayout->getFields() as $index => $craftField) {
            $fields[] = $this->createField($layout, $viewMode, $index, $craftField);
        }
        return $fields;
    }

    protected function createField(Layout $layout, ViewMode $viewMode, int $order, CraftField $craftField)
    {
        $class = get_class($craftField);
        $displayer = $this->getDefaultDisplayer($class);
        $field = new Field([
            'viewMode' => $viewMode->id,
            'field' => $craftField->handle,
            'displayerHandle' => $displayer ? $displayer->handle : '',
            'hidden' => ($displayer == null),
            'order' => $order,
            'options' => $displayer ? $displayer->getOptions()->toArray() : [],
            'layout' => $layout->id
        ]);
        $this->save($field);
    }

    protected function createTitleField(Layout $layout, ViewMode $viewMode, int $order = 0)
    {
        $displayer = $this->getDefaultDisplayer(TitleField::class);
        $field = new Field([
            'viewMode' => $viewMode->id,
            'field' => 'title',
            'displayerHandle' => $displayer ? $displayer->handle : '',
            'hidden' => ($displayer == null),
            'order' => $order,
            'options' => $displayer ? $displayer->getOptions()->toArray() : [],
            'layout' => $layout->id
        ]);
        $this->save($field);
    }
}