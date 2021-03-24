<?php 

namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\events\FieldDisplayerEvent;
use Ryssbowh\CraftThemes\events\FieldEvent;
use Ryssbowh\CraftThemes\exceptions\FieldException;
use Ryssbowh\CraftThemes\models\DisplayField;
use Ryssbowh\CraftThemes\models\ViewMode;
use Ryssbowh\CraftThemes\models\layouts\Layout;
use Ryssbowh\CraftThemes\records\FieldRecord;
use craft\base\Field;
use craft\events\ConfigEvent;
use craft\fieldlayoutelements\TitleField;
use craft\helpers\StringHelper;

class FieldsService extends Service
{
    const EVENT_BEFORE_SAVE = 1;
    const EVENT_AFTER_SAVE = 2;
    const EVENT_BEFORE_APPLY_DELETE = 3;
    const EVENT_AFTER_DELETE = 4;
    const EVENT_BEFORE_DELETE = 5;
    const CONFIG_KEY = 'themes.fields';

    /**
     * Get a field by id
     * 
     * @param  int    $id
     * @return Field
     * @throws FieldException
     */
    public function getById(int $id): Field
    {
        $record = FieldRecord::find()->where(['id' => $id])->one();
        if ($record) {
            return $record->toModel();
        }
        throw FieldException::noId($id);
    }

    /**
     * Saves one field
     * 
     * @param  Field $field
     * @return bool
     */
    public function save(DisplayField $field, bool $validate = false): bool
    {
        if ($validate and !$field->validate()) {
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
        $field->setAttributes($record->getAttributes(), false);
    
        return true;
    }

    /**
     * Deletes one field
     * 
     * @param  Field $record
     * @return bool
     */
    public function delete(Field $record): bool
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
            $field->display_id = $data['display_id'] ? $this->displayService()->getRecordByUid($data['display_id'])->id : null;
            $field->displayerHandle = $data['displayerHandle'];
            $field->fieldUid = $data['fieldUid'];
            $field->options = $data['options'];
            
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
     * Get field record by uid or a new one if not found
     * 
     * @param  string $uid
     * @return FieldRecord
     */
    public function getRecordByUid(string $uid): FieldRecord
    {
        return FieldRecord::findOne(['uid' => $uid]) ?? new FieldRecord;
    }
}