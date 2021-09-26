<?php

namespace Ryssbowh\CraftThemes\services;

use Illuminate\Support\Collection;
use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\events\DisplayEvent;
use Ryssbowh\CraftThemes\helpers\ProjectConfigHelper;
use Ryssbowh\CraftThemes\interfaces\DisplayInterface;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\interfaces\ViewModeInterface;
use Ryssbowh\CraftThemes\models\Display;
use Ryssbowh\CraftThemes\models\fields\CraftField;
use Ryssbowh\CraftThemes\models\fields\Matrix;
use Ryssbowh\CraftThemes\records\DisplayRecord;
use Ryssbowh\CraftThemes\records\LayoutRecord;
use craft\events\ConfigEvent;
use craft\events\FieldEvent;
use craft\events\RebuildConfigEvent;
use craft\fields\Matrix as CraftMatrix;
use craft\helpers\StringHelper;

class DisplayService extends Service
{
    const TYPE_FIELD = 'field';
    const TYPE_GROUP = 'group';
    const TYPES = [self::TYPE_FIELD, self::TYPE_GROUP];

    const EVENT_BEFORE_SAVE = 'before_save';
    const EVENT_AFTER_SAVE = 'after_save';
    const EVENT_BEFORE_APPLY_DELETE = 'before_apply_delete';
    const EVENT_AFTER_DELETE = 'after_delete';
    const EVENT_BEFORE_DELETE = 'before_delete';
    const CONFIG_KEY = 'themes.displays';

    /**
     * @var Collection
     */
    private $_displays;

    /**
     * Get all displays
     * 
     * @return Collection
     */
    public function all(): Collection
    {
        if ($this->_displays === null) {
            $records = DisplayRecord::find()->orderBy('order asc')->all();
            $this->_displays = collect();
            foreach ($records as $record) {
                $this->_displays->push($this->create($record));
            }
        }
        return $this->_displays;
    }

    /**
     * Create a display from config
     * 
     * @param  array|ActiveRecord $config
     * @return DisplayInterface
     */
    public function create($config): DisplayInterface
    {
        if ($config instanceof DisplayRecord) {
            $config = $config->getAttributes();
        }
        $itemData = $config['item'] ?? null;
        $display = new Display;
        $attributes = $display->safeAttributes();
        $config = array_intersect_key($config, array_flip($attributes));
        $display->setAttributes($config);
        if ($itemData) {
            if ($display->type == self::TYPE_FIELD) {
                $display->item = $this->fieldsService()->create($itemData);
            } else {
                $display->item = $this->groupsService()->create($itemData);
            }
            $display->item->display = $display;
        }
        return $display;
    }

    /**
     * Saves a display
     * 
     * @param  DisplayInterface $display
     * @param  bool             $validate
     * @return bool
     */
    public function save(DisplayInterface $display, bool $validate = true): bool
    {
        if ($validate and !$display->validate()) {
            return false;
        }

        $isNew = !is_int($display->id);

        $this->triggerEvent(self::EVENT_BEFORE_SAVE, new DisplayEvent([
            'display' => $display,
            'isNew' => $isNew
        ]));

        $projectConfig = \Craft::$app->getProjectConfig();
        $configData = $display->getConfig();
        $uid = $configData['uid'];
        $configPath = self::CONFIG_KEY . '.' . $uid;
        $projectConfig->set($configPath, $configData);

        $record = $this->getRecordByUid($uid);
        $display->setAttributes($record->getAttributes());
        
        if ($isNew) {
            //Sorting internal caches
            $this->add($display);
            $display->viewMode->displays = null;
        }

        $display->item->display = $display;
        if ($display->type == self::TYPE_FIELD) {
            $this->fieldsService()->save($display->item);
        } else {
            $this->groupsService()->save($display->item);
        }

        return true;
    }

    /**
     * Deletes a display
     * 
     * @param  DisplayInterface $display
     * @return bool
     */
    public function delete(DisplayInterface $display): bool
    {
        $this->triggerEvent(self::EVENT_BEFORE_DELETE, new DisplayEvent([
            'display' => $display
        ]));

        try {
            if ($display->type == self::TYPE_FIELD) {
                $this->fieldsService()->delete($display->item);
            } else {
                $this->groupsService()->delete($display->item);
            }
        } catch (\Throwable $e) {}

        \Craft::$app->getProjectConfig()->remove(self::CONFIG_KEY . '.' . $display->uid);

        $this->_displays = $this->all()->where('id', '!=', $display->id);
        $display->viewMode->displays = null;

        return true;
    }

    /**
     * Handles a change in display config
     * 
     * @param ConfigEvent $event
     */
    public function handleChanged(ConfigEvent $event)
    {
        ProjectConfigHelper::ensureAllViewModesProcessed();
        $uid = $event->tokenMatches[0];
        $data = $event->newValue;
        if (!$data) {
            //This can happen when fixing broken states
            return;
        }
        $transaction = \Craft::$app->getDb()->beginTransaction();
        try {
            $display = $this->getRecordByUid($uid);
            $isNew = $display->getIsNewRecord();

            $display->type = $data['type'];
            $display->order = $data['order'];
            $display->group_id = isset($data['group_id']) ? Themes::$plugin->groups->getByUid($data['group_id'])->id : null;
            $display->viewMode_id = Themes::$plugin->viewModes->getByUid($data['viewMode_id'])->id;
            $display->save(false);
            
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

    /**
     * Handles a deletion in display config
     * 
     * @param ConfigEvent $event
     */
    public function handleDeleted(ConfigEvent $event)
    {
        $uid = $event->tokenMatches[0];
        $display = $this->getRecordByUid($uid);

        $this->triggerEvent(self::EVENT_BEFORE_APPLY_DELETE, new DisplayEvent([
            'display' => $display
        ]));

        \Craft::$app->getDb()->createCommand()
            ->delete(DisplayRecord::tableName(), ['uid' => $uid])
            ->execute();

        $this->triggerEvent(self::EVENT_AFTER_DELETE, new DisplayEvent([
            'display' => $display
        ]));
    }

    /**
     * Respond to rebuild config event
     * 
     * @param RebuildConfigEvent $e
     */
    public function rebuildConfig(RebuildConfigEvent $e)
    {
        $parts = explode('.', self::CONFIG_KEY);
        foreach ($this->all() as $display) {
            $e->config[$parts[0]][$parts[1]][$display->uid] = $display->getConfig();
        }
    }

    /**
     * Callback when a field is deleted
     * Resave layout for which a deleted field was present
     * 
     * @param ConfigEvent $event
     */
    public function onCraftFieldDeleted(FieldEvent $event)
    {
        foreach ($this->getAllForCraftField($event->field->id) as $display) {
            $this->delete($display);
        }
    }

    /**
     * Handles a craft field save: If the type of field has changed
     * replaces the item in each display associated to the field.
     * Otherwise let the item handle the change.
     * 
     * @param FieldEvent $event
     */
    public function onCraftFieldSaved(FieldEvent $event)
    {
        if ($event->isNew) {
            return;
        }
        $field = $event->field;
        $displays = $this->getAllForCraftField($field->id);
        $toSave = [];
        foreach ($displays as $display) {
            $oldItem = $display->item;
            $layout = $display->viewMode->layout;
            $oldFieldClass = $oldItem->craft_field_class;
            if ($oldItem->craft_field_class != get_class($field)) {
                // Field has changed class, deleting old field, recreating it
                // and copying old field attributes
                $oldItem->delete();
                $display->item = $this->fieldsService()->createFromField($field);
                $display->item->setAttributes([
                    'id' => $oldItem->id,
                    'uid' => $oldItem->uid,
                    'labelHidden' => $oldItem->labelHidden,
                    'visuallyHidden' => $oldItem->labelVisuallyHidden,
                    'labelVisuallyHidden' => $oldItem->labelVisuallyHidden,
                    'hidden' => $display->item->hidden ?: $oldItem->hidden,
                    'display' => $display
                ]);
                $this->save($display);
            } else {
                // Let the field deal with the change itself
                if ($oldItem->onCraftFieldChanged($field)) {
                    $this->save($display);
                }
            }
        }
    }

    /**
     * Populate a display from post
     * 
     * @param  array $data
     * @return DisplayInterface
     */
    public function populateFromPost(array $data): DisplayInterface
    {
        $display = $this->getById($data['id']);
        $itemData = $data['item'];
        $type = $data['type'];
        unset($data['item']);
        unset($data['type']);
        $display->setAttributes($data);
        $itemData['display'] = $display;
        if ($type == self::TYPE_FIELD) {
            Themes::$plugin->fields->populateFromPost($itemData, $display);
        } else {
            Themes::$plugin->groups->populateFromPost($itemData, $display);
        }
        return $display;
    }

    /**
     * Get a display by id
     * 
     * @param  int    $id
     * @return ?DisplayInterface
     */
    public function getById(int $id): ?DisplayInterface
    {
        return $this->all()->firstWhere('id', $id);
    }

    /**
     * Get a display by uid
     * 
     * @param  int    $uid
     * @return ?DisplayInterface
     */
    public function getByUid(string $uid): ?DisplayInterface
    {
        return $this->all()->firstWhere('uid', $uid);
    }

    /**
     * Get all displays for a craft field id
     * 
     * @param  int    $fieldId
     * @return array
     */
    public function getAllForCraftField(int $fieldId): array
    {
        return $this->all()
            ->where('type', self::TYPE_FIELD)
            ->where('item.craft_field_id', $fieldId)
            ->values()
            ->all();
    }

    /**
     * Get all displays for a view mode and a field type
     * 
     * @param  ViewModeInterface $viewMode
     * @param  string            $type
     * @return ?DisplayInterface
     */
    public function getForFieldType(ViewModeInterface $viewMode, string $type): ?DisplayInterface
    {
        return $this->all()
            ->where('type', self::TYPE_FIELD)
            ->where('viewMode_id', $viewMode->id)
            ->firstWhere('item.type', $type);
    }

    /**
     * Get all displays for a layout
     * 
     * @param  LayoutInterface $layout
     * @return array
     */
    public function getForLayout(LayoutInterface $layout): array
    {
        if (!$layout->id) {
            return [];
        }
        $viewModes = array_map(function ($viewMode) {
            return $viewMode->id;
        }, $layout->viewModes);
        return $this->all()
            ->whereIn('viewMode_id', $viewModes)
            ->values()
            ->all();
    }

    /**
     * Get all displays for a group
     * 
     * @param  int $groupId
     * @return array
     */
    public function getForGroup(int $groupId): array
    {
        return $this->all()
            ->where('group_id', $groupId)
            ->values()
            ->all();
    }

    /**
     * Get all displays for a view mode
     * 
     * @param  ViewModeInterface $viewMode
     * @return array
     */
    public function getForViewMode(ViewModeInterface $viewMode): array
    {
        return $this->all()
            ->where('viewMode_id', $viewMode->id)
            ->where('group_id', null)
            ->values()
            ->all();
    }

    /**
     * Create all displays for a view mode
     * 
     * @param  ViewModeInterface $viewMode
     * @return array
     */
    public function createViewModeDisplays(ViewModeInterface $viewMode): array
    {
        //Keeping all the groups defined in this view mode 
        $displays = array_values(array_filter($this->getForViewMode($viewMode), function ($display) {
            return $display->type == self::TYPE_GROUP;
        }));
        //Catching errors when item is not defined on group. Discarding it if error
        foreach ($displays as $index => $display) {
            try {
                $display->getItem();
            } catch (\Throwable $e) {
                unset($displays[$index]);
            }
        }
        $order = $this->getNextOrder($viewMode);
        //Getting or creating displays for fields that are not craft fields (author, title etc)
        foreach (Themes::$plugin->fields->registeredFields as $fieldType => $fieldClass) {
            if ($fieldClass::shouldExistOnLayout($viewMode->layout)) {
                try {
                    //Catching errors when the display or its item are not defined
                    $display = $this->getForFieldType($viewMode, $fieldType);
                    $display->getItem();
                } catch (\Throwable $e) {
                    $display = null;
                }
                if (!$display) {
                    $display = $this->create([
                        'type' => self::TYPE_FIELD,
                        'viewMode' => $viewMode,
                        'order' => $order,
                    ]);
                    $order++;
                    $display->item = $fieldClass::create();
                }
                $displays[] = $display;
            }
        }
        //Getting or creating displays for craft fields
        foreach ($viewMode->layout->getCraftFields() as $craftField) {
            try {
                //Catching errors when the display or its item are not defined
                $display = $this->getForCraftField($craftField->id, $viewMode);
                $display->getItem();
            } catch (\Throwable $e) {
                $display = null;
            }
            if (!$display) {
                $display = $this->create([
                    'type' => self::TYPE_FIELD,
                    'viewMode' => $viewMode,
                    'order' => $order,
                ]);
                $order++;
                $display->item = Themes::$plugin->fields->createFromField($craftField);
            }
            $displays[] = $display;
        }

        return $displays;
    }

    /**
     * Clean up for view mode, deletes old displays
     *
     * @param array $displays
     * @param ViewModeInterface $viewMode
     */
    public function cleanUp(array $displays, ViewModeInterface $viewMode)
    {
        $toKeep = array_map(function ($display) {
            return $display->id;
        }, $displays);
        $toDelete = $this->all()
            ->whereNotIn('id', $toKeep)
            ->where('viewMode_id', $viewMode->id)
            ->all();
        foreach ($toDelete as $display) {
            $this->delete($display);
        }
    }

    /**
     * Add a display to internal cache
     * 
     * @param DisplayInterface $layout
     */
    protected function add(DisplayInterface $display)
    {
        if (!$this->all()->firstWhere('id', $display->id)) {
            $this->all()->push($display);
        }
    }

    /**
     * Get a display for a craft field and a view mode
     * 
     * @param  int|null          $fieldId
     * @param  ViewModeInterface $viewMode
     * @return ?DisplayInterface
     */
    protected function getForCraftField(?int $fieldId = null, ViewModeInterface $viewMode): ?DisplayInterface
    {
        return $this->all()
            ->where('type', self::TYPE_FIELD)
            ->where('item.craft_field_id', $fieldId)
            ->firstWhere('viewMode_id', $viewMode->id);
    }

    /**
     * Get next order in displays for a view mode
     * 
     * @param  ViewModeInterface $viewMode
     * @return int
     */
    protected function getNextOrder(ViewModeInterface $viewMode): ?int
    {
        $displays = $this->getForViewMode($viewMode);
        if (sizeof($displays) > 0) {
            return $displays[sizeof($displays) - 1]->order + 1;
        }
        return 0;
    }

    /**
     * Get field record by uid or a new one if not found
     * 
     * @param  string $uid
     * @return DisplayRecord
     */
    public function getRecordByUid(string $uid): DisplayRecord
    {
        return DisplayRecord::findOne(['uid' => $uid]) ?? new DisplayRecord(['uid' => $uid]);
    }
}