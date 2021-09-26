<?php

namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\events\ViewModeEvent;
use Ryssbowh\CraftThemes\exceptions\ViewModeException;
use Ryssbowh\CraftThemes\helpers\ProjectConfigHelper;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\interfaces\ViewModeInterface;
use Ryssbowh\CraftThemes\models\ViewMode;
use Ryssbowh\CraftThemes\records\LayoutRecord;
use Ryssbowh\CraftThemes\records\ViewModeRecord;
use Ryssbowh\CraftThemes\services\LayoutService;
use craft\events\ConfigEvent;
use craft\events\RebuildConfigEvent;
use craft\helpers\StringHelper;

class ViewModeService extends Service
{
    const EVENT_BEFORE_SAVE = 'before_save';
    const EVENT_AFTER_SAVE = 'after_save';
    const EVENT_BEFORE_APPLY_DELETE = 'before_apply_delete';
    const EVENT_AFTER_DELETE = 'after_delete';
    const EVENT_BEFORE_DELETE = 'before_delete';
    const CONFIG_KEY = 'themes.viewModes';
    const DEFAULT_HANDLE = 'default';

    /**
     * @var Collection
     */
    protected $_viewModes;

    /**
     * Get all view modes
     * 
     * @return Collection
     */
    public function all()
    {
        if ($this->_viewModes === null) {
            $records = ViewModeRecord::find()->all();
            $this->_viewModes = collect();
            foreach ($records as $record) {
                $this->_viewModes->push($this->create($record));
            }
        }
        return $this->_viewModes;
    }

    /**
     * Creates a view mode from config
     * 
     * @param  array|ViewModeRecord $config
     * @return ViewModeInterface
     */
    public function create($config): ViewModeInterface
    {
        if ($config instanceof ViewModeRecord) {
            $config = $config->getAttributes();
        }
        $displayData = null;
        if (isset($config['displays'])) {
            $displayData = $config['displays'];
            unset($config['displays']);
        }
        $viewMode = new ViewMode;
        $config = array_intersect_key($config, array_flip($viewMode->safeAttributes()));
        $viewMode->setAttributes($config);
        if ($displayData) {
            $displays = [];
            foreach ($displayData as $data) {
                $displays[] = $this->displayService()->create($data);
            }
            $viewMode->displays = $displays;
        }
        return $viewMode;
    }

    /**
     * Save a view mode
     * 
     * @param  ViewModeInterface $viewMode
     * @param  bool              $validate
     * @throws ViewModeException
     * @return bool
     */
    public function save(ViewModeInterface $viewMode, bool $validate = true): bool
    {
        if ($validate and !$viewMode->validate()) {
            return false;
        }

        if ($viewMode->layout->type == LayoutService::DEFAULT_HANDLE) {
            throw ViewModeException::defaultLayoutNoViewModes($viewMode->layout);
        }

        $exists = $this->all()
            ->where('layout_id', $viewMode->layout->id)
            ->firstWhere('handle', $viewMode->handle);
        if ($exists and $exists->id != $viewMode->id) {
            throw ViewModeException::duplicatedHandle($exists->id, $viewMode->handle);
        }

        $isNew = !is_int($viewMode->id);
        
        $this->triggerEvent(self::EVENT_BEFORE_SAVE, new ViewModeEvent([
            'viewMode' => $viewMode,
            'isNew' => $isNew
        ]));

        $projectConfig = \Craft::$app->getProjectConfig();
        $configData = $viewMode->getConfig();
        $uid = $viewMode->uid ?? StringHelper::UUID();
        $configPath = self::CONFIG_KEY . '.' . $uid;
        $projectConfig->set($configPath, $configData);

        $record = $this->getRecordByUid($uid);
        $viewMode->setAttributes($record->getAttributes());
        
        if ($isNew) {
            //Sorting internal caches
            $this->add($viewMode);
            $viewMode->layout->viewModes = null;
        }

        //Saving displays, groups first
        $groups = [];
        $displays = [];
        $allDisplays = $viewMode->displays;
        foreach ($allDisplays as $display) {
            if ($display->type == DisplayService::TYPE_GROUP) {
                $groups[] = $display;
            } else {
                $displays[] = $display;
            }
        }
        foreach ($groups as $display) {
            Themes::$plugin->displays->save($display);
            //Adding all group displays in the list so they are not deleted by the cleanup
            $allDisplays = array_merge($allDisplays, $display->item->displays);
        }
        foreach ($displays as $display) {
            Themes::$plugin->displays->save($display);
        }
        Themes::$plugin->displays->cleanUp($allDisplays, $viewMode);

        return true;
    }

    /**
     * Deletes a view mode
     * 
     * @param  ViewModeInterface $viewMode
     * @param  bool              $force
     * @return bool
     * @throws ViewModeException
     */
    public function delete(ViewModeInterface $viewMode, bool $force = false): bool
    {
        if (!$force and ($viewMode->handle == self::DEFAULT_HANDLE)) {
            throw ViewModeException::defaultUndeletable();
        }

        $this->triggerEvent(self::EVENT_BEFORE_DELETE, new ViewModeEvent([
            'viewMode' => $viewMode
        ]));

        //Deleting displays, groups last
        $groups = [];
        $displays = [];
        foreach ($viewMode->displays as $display) {
            if ($display->type == DisplayService::TYPE_GROUP) {
                $groups[] = $display;
            } else {
                $displays[] = $display;
            }
        }
        foreach ($displays as $display) {
            Themes::$plugin->displays->delete($display);
        }
        foreach ($groups as $display) {
            Themes::$plugin->displays->delete($display);
        }

        \Craft::$app->getProjectConfig()->remove(self::CONFIG_KEY . '.' . $viewMode->uid);

        $this->_viewModes = $this->all()->where('id', '!=', $viewMode->id);
        $viewMode->layout->viewModes = null;

        return true;
    }

    /**
     * Handles a change in view mode config
     * 
     * @param ConfigEvent $event
     */
    public function handleChanged(ConfigEvent $event)
    {
        ProjectConfigHelper::ensureAllLayoutsProcessed();
        $uid = $event->tokenMatches[0];
        $data = $event->newValue;
        if (!$data) {
            //This can happen when fixing broken states
            return;
        }
        $transaction = \Craft::$app->getDb()->beginTransaction();
        try {
            $viewMode = $this->getRecordByUid($uid);
            $isNew = $viewMode->getIsNewRecord();

            $viewMode->handle = $data['handle'];
            $viewMode->name = $data['name'];
            $viewMode->layout_id = Themes::$plugin->layouts->getRecordByUid($data['layout_id'])->id;
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
     * @param ConfigEvent $event
     */
    public function handleDeleted(ConfigEvent $event)
    {
        $uid = $event->tokenMatches[0];
        $viewMode = $this->getRecordByUid($uid);

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
     * Respond to rebuild config event
     * 
     * @param RebuildConfigEvent $e
     */
    public function rebuildConfig(RebuildConfigEvent $e)
    {
        $parts = explode('.', self::CONFIG_KEY);
        foreach ($this->all() as $viewMode) {
            $e->config[$parts[0]][$parts[1]][$viewMode->uid] = $viewMode->getConfig();
        }
    }

    /**
     * Populates a view mode from posted data
     * 
     * @param  array $data
     * @return ViewModeInterface
     */
    public function populateFromPost(array $data): ViewModeInterface
    {
        $viewMode = $this->getById($data['id']);
        $displaysData = $data['displays'];
        unset($data['displays']);
        $viewMode->setAttributes($data);
        $displays = [];
        foreach ($displaysData as $data) {
            if ($data['id'] ?? null) {
                $displays[] = Themes::$plugin->displays->populateFromPost($data);
            } else {
                $displays[] = Themes::$plugin->displays->create($data);
            }
        }
        $viewMode->displays = $displays;
        return $viewMode;
    }

    /**
     * Clean up for layout, deletes old view modes
     *
     * @param array $viewModes
     * @param LayoutInterface $layout
     */
    public function cleanUp(array $viewModes, LayoutInterface $layout)
    {
        $toKeep = array_map(function ($viewMode) {
            return $viewMode->id;
        }, $viewModes);
        $toDelete = $this->all()
            ->whereNotIn('id', $toKeep)
            ->where('layout_id', $layout->id)
            ->all();
        foreach ($toDelete as $viewMode) {
            $this->delete($viewMode, true);
        }
    }

    /**
     * Get view mode by id
     * 
     * @param  int    $id
     * @return ViewModeInterface
     * @throws ViewModeException
     */
    public function getById(int $id): ViewModeInterface
    {
        if ($viewMode = $this->all()->firstWhere('id', $id)) {
            return $viewMode;
        }
        throw ViewModeException::noId($id);
    }

    /**
     * Get view mode by uid
     * 
     * @param  int    $id
     * @return ViewModeInterface
     * @throws ViewModeException
     */
    public function getByUid(string $uid): ViewModeInterface
    {
        if ($viewMode = $this->all()->firstWhere('uid', $uid)) {
            return $viewMode;
        }
        throw ViewModeException::noUid($uid);
    }

    /**
     * Get all view modes for a layout
     * 
     * @param  LayoutInterface $layout
     * @return array
     */
    public function getForLayout(LayoutInterface $layout): array
    {
        if (!$layout->id) {
            return [];
        }
        return $this->all()
            ->where('layout.id', $layout->id)
            ->values()
            ->all();
    }

    /**
     * Get a default view mode
     * 
     * @param  LayoutInterface $layout
     * @return ?ViewModeInterface
     */
    public function getDefault(LayoutInterface $layout): ?ViewModeInterface
    {
        return $this->get($layout);
    }

    /**
     * Get a view mode
     * 
     * @param  LayoutInterface $layout
     * @param  string $handle
     * @return ?ViewModeInterface
     */
    public function get(LayoutInterface $layout, string $handle = self::DEFAULT_HANDLE): ?ViewModeInterface
    {
        if (!$layout->id) {
            return null;
        }
        return $this->all()
            ->where('layout.id', $layout->id)
            ->firstWhere('handle', $handle);
    }

    /**
     * Get view mode record by uid, or a new one if it's not found
     * 
     * @param  string $uid
     * @return ViewModeRecord
     */
    public function getRecordByUid(string $uid): ViewModeRecord
    {
        return ViewModeRecord::findOne(['uid' => $uid]) ?? new ViewModeRecord(['uid' => $uid]);
    }

    /**
     * Add a view mode to internal cache
     * 
     * @param ViewModeInterface $layout
     */
    protected function add(ViewModeInterface $viewMode)
    {
        if (!$this->all()->firstWhere('id', $viewMode->id)) {
            $this->all()->push($viewMode);
        }
    }
}