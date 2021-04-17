<?php 

namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\events\DisplayEvent;
use Ryssbowh\CraftThemes\exceptions\DisplayException;
use Ryssbowh\CraftThemes\models\Display;
use Ryssbowh\CraftThemes\models\ViewMode;
use Ryssbowh\CraftThemes\models\fields\Title;
use Ryssbowh\CraftThemes\models\layouts\Layout;
use Ryssbowh\CraftThemes\records\DisplayRecord;
use Ryssbowh\CraftThemes\records\FieldRecord;
use Ryssbowh\CraftThemes\records\GroupRecord;
use Ryssbowh\CraftThemes\records\MatrixPivotRecord;
use Ryssbowh\CraftThemes\records\ViewModeRecord;
use craft\base\Field;
use craft\db\ActiveRecord;
use craft\events\ConfigEvent;
use craft\events\EntryTypeEvent;
use craft\fieldlayoutelements\TitleField;
use craft\fields\Matrix;
use craft\helpers\StringHelper;
use craft\models\MatrixBlockType;

class DisplayService extends Service
{
    const TYPE_FIELD = 'field';
    const TYPE_GROUP = 'group';
    const TYPES = [self::TYPE_FIELD, self::TYPE_GROUP];

    const EVENT_BEFORE_SAVE = 1;
    const EVENT_AFTER_SAVE = 2;
    const EVENT_BEFORE_APPLY_DELETE = 3;
    const EVENT_AFTER_DELETE = 4;
    const EVENT_BEFORE_DELETE = 5;

    const CONFIG_KEY = 'themes.display';

    private $_displays;

    public function all()
    {
        if ($this->_displays === null) {
            $records = DisplayRecord::find()->all();
            $this->_displays = collect();
            foreach ($records as $record) {
                $this->_displays->push($this->create($record));
            }
        }
        return $this->_displays;
    }

    public function create($config): Display
    {
        if ($config instanceof ActiveRecord) {
            $config = $config->getAttributes();
        }
        $itemData = $config['item'] ?? null;
        $display = new Display;
        $attributes = $display->safeAttributes();
        $config = array_intersect_key($config, array_flip($attributes));
        $display->setAttributes($config);
        if ($itemData) {
            $display->item = $this->fieldsService()->create($itemData);
        }
        return $display;
    }

    public function install()
    {
        foreach ($this->layoutService()->withDisplays() as $layout) {
            $this->installLayoutDisplays($layout);
        }
    }

    public function getById(int $id): ?Display
    {
        return $this->all()->firstWhere('id', $id);
    }

    public function getForCraftField(?int $fieldId = null, ViewMode $viewMode): ?Display
    {
        return $this->all()
            ->where('type', self::TYPE_FIELD)
            ->where('item.craft_field_id', $fieldId)
            ->firstWhere('viewMode_id', $viewMode->id);
    }

    public function getAllForCraftField(int $fieldId): array
    {
        return $this->all()
            ->where('type', self::TYPE_FIELD)
            ->where('item.craft_field_id', $fieldId)
            ->values()
            ->all();
    }

    public function getForTitleField(ViewMode $viewMode = null): ?Display
    {
        return $this->all()
            ->where('type', self::TYPE_FIELD)
            ->where('viewMode_id', $viewMode->id)
            ->firstWhere('item.type', FieldsService::TYPE_TITLE);
    }

    public function getForLayout(Layout $layout): array
    {
        $viewModes = array_map(function ($viewMode) {
            return $viewMode->id;
        }, $layout->viewModes);
        return $this->all()
            ->whereIn('viewMode_id', $viewModes)
            ->values()
            ->all();
    }

    /**
     * Get all displays for a view mode
     * 
     * @param  ViewMode $viewMode
     * @return array
     */
    public function getForViewMode(ViewMode $viewMode): array
    {
        return $this->all()
            ->where('viewMode_id', $viewMode->id)
            ->values()
            ->all();
    }

    /**
     * Delete all displays which id is not in $toKeep for a layout
     * 
     * @param array  $toKeep
     * @param Layout $layout
     */
    public function deleteForLayout(Layout $layout, array $toKeep = [])
    {
        $viewModeIds = array_map(function ($viewMode) {
            return $viewMode->id;
        }, $layout->viewModes);
        $displays = $this->all()
            ->whereIn('viewMode_id', $viewModeIds)
            ->whereNotIn('id', $toKeep);
        foreach ($displays as $display) {
            $this->delete($display);
        }
    }

    /**
     * Saves one display
     * 
     * @param  Display $display
     * @return bool
     */
    public function save(Display $display, bool $validate = true): bool
    {
        if ($validate and !($display->validate() and $display->item->validate())) {
            throw DisplayException::onSave($display);
        }
        
        $isNew = !is_int($display->id);
        $uid = $isNew ? StringHelper::UUID() : $display->uid;

        $this->triggerEvent(self::EVENT_BEFORE_SAVE, new DisplayEvent([
            'display' => $display
        ]));

        $projectConfig = \Craft::$app->getProjectConfig();
        $configData = $display->getConfig();
        $configPath = self::CONFIG_KEY . '.' . $uid;
        $projectConfig->set($configPath, $configData);

        $record = $this->getRecordByUid($uid);
        $display->setAttributes($record->getAttributes());
        $display->item = null;

        if ($isNew) {
            $this->_displays->push($display);
        }
                
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
        if (!$data) {
            //For some reason I don't understand, deleting a display config during an install will trigger an update event and end up here
            //with a null config. It might be because the transaction is not committed yet I'm not sure.
            return;
        }
        $transaction = \Craft::$app->getDb()->beginTransaction();
        try {
            $display = $this->getRecordByUid($uid);
            $isNew = $display->getIsNewRecord();

            $display->uid = $uid;
            $display->viewMode_id = $this->viewModesService()->getRecordByUid($data['viewMode_id'])->id;
            $display->type = $data['type'];
            $display->order = $data['order'];
            $display->save(false);

            $this->fieldsService()->handleChanged($display->id, $data['item']);

            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }

        $this->triggerEvent(self::EVENT_AFTER_SAVE, new DisplayEvent([
            'display' => $display,
            'isNew' => $isNew,
        ]));
    }

    public function delete(Display $display): bool
    {
        $this->triggerEvent(self::EVENT_BEFORE_DELETE, new DisplayEvent([
            'display' => $display
        ]));
        \Craft::$app->getProjectConfig()->remove(self::CONFIG_KEY . '.' . $display->uid);

        $this->_displays = $this->all()->where('id', '!=', $display->id);

        return true;
    }

    /**
     * Hanles field config deletion
     * 
     * @param ConfigEvent $event
     */
    public function handleDeleted(ConfigEvent $event)
    {
        $uid = $event->tokenMatches[0];
        $record = $this->getRecordByUid($uid);

        if (!$record) {
            return;
        }

        $this->triggerEvent(self::EVENT_BEFORE_APPLY_DELETE, new DisplayEvent([
            'display' => $record
        ]));

        \Craft::$app->getDb()->createCommand()
            ->delete(DisplayRecord::tableName(), ['uid' => $uid])
            ->execute();

        $this->triggerEvent(self::EVENT_AFTER_DELETE, new DisplayEvent([
            'display' => $record
        ]));
    }

    /**
     * Creates a entry type or category layout displays
     * 
     * @param EntryTypeEvent $event
     */
    public function onCraftElementSaved(string $type, string $uid)
    {
        foreach ($this->themesRegistry()->getNonPartials() as $theme) {
            $layout = $this->layoutService()->get($theme->handle, $type, $uid);
            $this->installLayoutDisplays($layout);
        }
    }

    /**
     * Delete field records
     * 
     * @param  ConfigEvent $event
     */
    public function onCraftFieldDeleted(CraftField $field)
    {
        foreach ($this->getAllForCraftField($field->id) as $display) {
            $this->delete($display);
        }
    }

    public function onCraftFieldSaved(CraftField $craftField)
    {
        $displays = $this->getAllForCraftField($craftField->id);
        if ($event->oldValue['type'] !== $event->newValue['type']) {
            foreach ($displays as $oldDisplay) {
                $viewMode = $oldDisplay->viewMode;
                $order = $oldDisplay->order;
                $this->delete($oldDisplay);
                $display = $this->createDisplay($viewMode, $craftField, $order);
                $this->save($display);
            }
        }
    }

    /**
     * Create all displays for a layout.
     * Go through all craft fields defined on the section/category and create display fields for it.
     * 
     * @param  Layout $layout
     */
    protected function installLayoutDisplays(Layout $layout)
    {
        $displayIds = [];
        foreach ($layout->viewModes as $viewMode) {
            $order = $this->getMaxOrder($viewMode) ?? 0;
            if (!$display = $this->getForTitleField($viewMode)) {
                $order++;
                $display = $this->create([
                    'type' => self::TYPE_FIELD,
                    'viewMode_id' => $viewMode->id,
                    'order' => $order,
                ]);
                $display->item = $this->fieldsService()->createTitleField();
                $this->save($display);
            }
            $displayIds[] = $display->id;
            foreach ($layout->getFieldLayout()->getFields() as $craftField) {
                if (!$display = $this->getForCraftField($craftField->id, $viewMode)) {
                    $order++;
                    $display = $this->create([
                        'type' => self::TYPE_FIELD,
                        'viewMode_id' => $viewMode->id,
                        'order' => $order,
                    ]);
                    $display->item = $this->fieldsService()->createField($craftField);
                    $this->save($display);
                }
                $displayIds[] = $display->id;
            }
        }
        $this->deleteForLayout($layout, $displayIds);
    }

    /**
     * Get max order in displays for a view mode
     * 
     * @param  ViewMode $viewMode
     * @return int
     */
    protected function getMaxOrder(ViewMode $viewMode): ?int
    {
        $size = sizeof($this->getForViewMode($viewMode));
        if ($size > 0) {
            return $this->getForViewMode($viewMode)[$size - 1]->order;
        }
        return null;
    }

    /**
     * Get field record by uid or a new one if not found
     * 
     * @param  string $uid
     * @return DisplayRecord
     */
    protected function getRecordByUid(string $uid): DisplayRecord
    {
        return DisplayRecord::findOne(['uid' => $uid]) ?? new DisplayRecord;
    }
}