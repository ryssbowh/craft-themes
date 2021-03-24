<?php 

namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\events\ViewModeEvent;
use Ryssbowh\CraftThemes\exceptions\ViewModeException;
use Ryssbowh\CraftThemes\models\ViewMode;
use Ryssbowh\CraftThemes\models\layouts\Layout;
use Ryssbowh\CraftThemes\records\ViewModeRecord;
use craft\events\ConfigEvent;
use craft\helpers\StringHelper;

class ViewModeService extends Service
{
    const EVENT_BEFORE_SAVE = 1;
    const EVENT_AFTER_SAVE = 2;
    const EVENT_BEFORE_APPLY_DELETE = 3;
    const EVENT_AFTER_DELETE = 4;
    const EVENT_BEFORE_DELETE = 5;
    const CONFIG_KEY = 'themes.viewModes';
    const DEFAULT_HANDLE = 'default';

    protected $viewModes;

    /**
     * Get all view modes
     * 
     * @return array
     */
    public function getAll(): array
    {
        if ($this->viewModes === null) {
            $this->viewModes = [];
            foreach (ViewModeRecord::find()->all() as $record) {
                $this->viewModes[] = $record->toModel();
            }
        }
        return $this->viewModes;
    }

    /**
     * Get view mode by id
     * 
     * @param  int    $id
     * @return ViewMode
     * @throws ViewModeException
     */
    public function getById(int $id): ViewMode
    {
        foreach ($this->getAll() as $viewMode) {
            if ($viewMode->id == $id) {
                return $viewMode;
            }
        }
        throw ViewModeException::noId($id);
    }

    /**
     * Get all view modes for a layout
     * 
     * @param  Layout $layout
     * @return array
     */
    public function forLayout(Layout $layout): array
    {
        $viewModes = array_values(array_filter($this->getAll(), function ($viewMode) use ($layout) {
            return ($viewMode->layout_id == $layout->id);
        }));
        if (!$viewModes) {
            $viewModes[] = $this->create($layout, ViewModeService::DEFAULT_HANDLE);
        }
        return $viewModes;
    }

    /**
     * Delete all view modes which id is not in $toKeep for a layout
     * 
     * @param array  $toKeep
     * @param Layout $layout
     */
    public function deleteForLayout(Layout $layout, array $toKeep = [])
    {
        $viewModes = ViewModeRecord::find()->where([
            'layout_id' => $layout->id
        ])->andWhere(['not in', 'id', $toKeep])->all();
        foreach ($viewModes as $viewMode) {
            $this->delete($viewMode->toModel());
        }
    }

    /**
     * Get a default view mode
     * 
     * @param  Layout $layout
     * @return array
     */
    public function getDefault(Layout $layout): ?ViewMode
    {
        return $this->get($layout);
    }

    /**
     * Get a view mode
     * 
     * @param  Layout $layout
     * @param  string $handle
     * @return array
     */
    public function get(Layout $layout, string $handle = self::DEFAULT_HANDLE): ?ViewMode
    {
        foreach ($this->forLayout($layout) as $viewMode) {
            if ($viewMode->handle == $handle) {
                return $viewMode;
            }
        }
        return null;
    }

    /**
     * Get view mode from array of data
     * 
     * @param  array  $data
     * @return ViewMode
     */
    public function fromData(array $data): ViewMode
    {
        unset($data['uid']);
        if (isset($data['id'])) {
            $viewMode = $this->getById($data['id']);
            $viewMode->name = $data['name'];
        } else {
            $viewMode = new ViewMode($data);
        }
        return $viewMode;
    }

    public function deleteAll(array $toKeep = [])
    {
        $viewModes = array_filter($this->viewModes, function ($viewMode) use ($toKeep) {
            return (!$viewMode->handle == 'default' and !in_array($viewMode->id, $toKeep));
        });
        foreach ($viewModes as $viewMode) {
            $this->delete($viewMode);
        }
    }

    /**
     * Save a view mode
     * 
     * @param  ViewMode $viewMode
     * @return ViewMode
     */
    public function save(ViewMode $viewMode): ViewMode
    {
        $isNew = !is_int($viewMode->id);
        $uid = $isNew ? StringHelper::UUID() : $viewMode->uid;

        $this->triggerEvent(self::EVENT_BEFORE_SAVE, new ViewModeEvent([
            'viewMode' => $viewMode
        ]));

        $projectConfig = \Craft::$app->getProjectConfig();
        $configData = $viewMode->getConfig();
        $configPath = self::CONFIG_KEY . '.' . $uid;
        $projectConfig->set($configPath, $configData);

        $record = $this->getRecordByUid($uid);
        $record->save(false);
        
        if ($isNew) {
            $viewMode->setAttributes($record->getAttributes(), false);
            $this->viewModes[] = $viewMode;
        }

        return $viewMode;
    }

    /**
     * Get view mode record by uid, or a new one if it's not found
     * 
     * @param  string $uid
     * @return ViewModeRecord
     */
    public function getRecordByUid(string $uid): ViewModeRecord
    {
        return ViewModeRecord::findOne(['uid' => $uid]) ?? new ViewModeRecord;
    }

    /**
     * Deletes a layout
     * 
     * @param  ViewMode $layout
     * @return bool
     * @throws LayoutException
     */
    public function delete(ViewMode $viewMode): bool
    {
        if ($viewMode->handle == ViewModeService::DEFAULT_HANDLE) {
            throw ViewModeException::defaultUndeletable();
        }
        $this->triggerEvent(self::EVENT_BEFORE_DELETE, new ViewModeEvent([
            'viewMode' => $viewMode
        ]));
        \Craft::$app->getProjectConfig()->remove(self::CONFIG_KEY . '.' . $viewMode->uid);

        foreach ($this->viewModes as $index => $viewMode2) {
            if ($viewMode2->id === $viewMode->id) {
                unset($this->viewModes[$index]);
            }
        }
        return true;
    }

    /**
     * Handles a change in view mode config
     * 
     * @param  ConfigEvent $event
     */
    public function handleChanged(ConfigEvent $event)
    {
        $uid = $event->tokenMatches[0];
        $data = $event->newValue;
        $transaction = \Craft::$app->getDb()->beginTransaction();

        try {
            $viewMode = $this->getRecordByUid($uid);
            $isNew = $viewMode->getIsNewRecord();

            $viewMode->uid = $uid;
            $viewMode->handle = $data['handle'];
            $viewMode->layout_id = $this->layoutService()->getRecordByUid($data['layout_id'])->id;
            $viewMode->name = $data['name'];
            
            $viewMode->save(false);
            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }

        $this->triggerEvent(self::EVENT_AFTER_SAVE, new ViewModeEvent([
            'viewMode' => $viewMode,
            'isNew' => $isNew,
        ]));
    }

    /**
     * Handles a deletion in view mode config
     * 
     * @param  ConfigEvent $event
     */
    public function handleDeleted(ConfigEvent $event)
    {
        $uid = $event->tokenMatches[0];
        $viewMode = $this->getRecordByUid($uid);

        if (!$viewMode) {
            return;
        }

        $this->triggerEvent(self::EVENT_BEFORE_APPLY_DELETE, new ViewModeEvent([
            'viewMode' => $viewMode
        ]));

        \Craft::$app->getDb()->createCommand()
            ->delete(ViewModeRecord::tableName(), ['uid' => $uid])
            ->execute();

        $this->triggerEvent(self::EVENT_AFTER_DELETE, new ViewModeEvent([
            'viewMode' => $viewMode
        ]));
    }

    /**
     * Creates a view mode for a layout
     * 
     * @param  Layout $layout
     * @param  string $handle
     * @param  string $name
     * @return ViewMode
     */
    public function create(Layout $layout, string $handle = ViewModeService::DEFAULT_HANDLE, string $name = 'Default'): ViewMode
    {
        $viewMode = new ViewMode([
            'handle' => $handle,
            'name' => \Craft::t('themes', $name),
            'layout_id' => $layout->id
        ]);
        $this->save($viewMode);
        $viewModes = $this->viewModes;
        array_unshift($viewModes, $viewMode);
        $this->viewModes = $viewModes;
        return $viewMode;
    }
}