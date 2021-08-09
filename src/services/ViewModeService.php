<?php 

namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\events\ViewModeEvent;
use Ryssbowh\CraftThemes\exceptions\ViewModeException;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\ViewMode;
use Ryssbowh\CraftThemes\records\LayoutRecord;
use Ryssbowh\CraftThemes\records\ViewModeRecord;
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
     * @param  array|ActiveRecord $config
     * @return ViewMode
     */
    public function create($config): ViewMode
    {
        if ($config instanceof ActiveRecord) {
            $config = $config->getAttributes();
        }
        $config['uid'] = $config['uid'] ?? StringHelper::UUID();
        return new ViewMode($config);
    }

    /**
     * Save a view mode
     * 
     * @param  ViewMode $viewMode
     * @param  bool $validate
     * @return bool
     */
    public function save(ViewMode $viewMode, bool $validate = true): bool
    {
        if ($validate and !$viewMode->validate()) {
            return false;
        }

        $isNew = !is_int($viewMode->id);
        $uid = $viewMode->uid;

        $this->triggerEvent(self::EVENT_BEFORE_SAVE, new ViewModeEvent([
            'viewMode' => $viewMode,
            'isNew' => $isNew
        ]));

        $projectConfig = \Craft::$app->getProjectConfig();
        $configData = $viewMode->getConfig();
        $configPath = self::CONFIG_KEY . '.' . $uid;
        $projectConfig->set($configPath, $configData);

        $record = $this->getRecordByUid($uid);
        $viewMode->setAttributes($record->getAttributes());
        
        if ($isNew) {
            $this->add($viewMode);
        }

        return true;
    }

    /**
     * Deletes a view mode
     * 
     * @param  ViewMode $layout
     * @param  bool     $force
     * @return bool
     * @throws ViewModeException
     */
    public function delete(ViewMode $viewMode, bool $force = false): bool
    {
        if (!$force and ($viewMode->handle == self::DEFAULT_HANDLE)) {
            throw ViewModeException::defaultUndeletable();
        }
        $this->triggerEvent(self::EVENT_BEFORE_DELETE, new ViewModeEvent([
            'viewMode' => $viewMode
        ]));

        \Craft::$app->getProjectConfig()->remove(self::CONFIG_KEY . '.' . $viewMode->uid);

        $this->_viewModes = $this->all()->where('id', '!=', $viewMode->id);

        return true;
    }

    /**
     * Handles a change in view mode config
     * 
     * @param ConfigEvent $event
     */
    public function handleChanged(ConfigEvent $event)
    {
        $uid = $event->tokenMatches[0];
        $data = $event->newValue;
        $transaction = \Craft::$app->getDb()->beginTransaction();
        try {
            $viewMode = $this->getRecordByUid($uid);
            $isNew = $viewMode->getIsNewRecord();

            $viewMode->handle = $data['handle'];
            $viewMode->name = $data['name'];
            $viewMode->layout_id = Themes::$plugin->layouts->getByUid($data['layout_id'])->id;
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
        foreach ($this->all() as $viewMode) {
            $e->config[self::CONFIG_KEY.'.'.$viewMode->uid] = $viewMode->getConfig();
        }
    }

    /**
     * Clean up for layout, deletes old view modes
     * 
     * @param LayoutInterface $layout
     */
    public function cleanUpLayout(LayoutInterface $layout)
    {
        $toKeep = array_map(function ($viewMode) {
            return $viewMode->id;
        }, $layout->viewModes);
        $toDelete = $this->all()
            ->whereNotIn('id', $toKeep)
            ->where('layout_id', $layout->id)
            ->all();
        foreach ($toDelete as $viewMode) {
            $this->delete($viewMode);
        }
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
        if ($viewMode = $this->all()->firstWhere('id', $id)) {
            return $viewMode;
        }
        throw ViewModeException::noId($id);
    }

    /**
     * Get view mode by uid
     * 
     * @param  int    $id
     * @return ViewMode
     * @throws ViewModeException
     */
    public function getByUid(string $uid): ViewMode
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
     * @return array
     */
    public function getDefault(LayoutInterface $layout): ?ViewMode
    {
        return $this->get($layout);
    }

    /**
     * Get a view mode
     * 
     * @param  LayoutInterface $layout
     * @param  string $handle
     * @return array
     */
    public function get(LayoutInterface $layout, string $handle = self::DEFAULT_HANDLE): ?ViewMode
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
     * @param ViewMode $layout
     */
    protected function add(ViewMode $viewMode)
    {
        if (!$this->all()->firstWhere('id', $viewMode->id)) {
            $this->all()->push($viewMode);
        }
    }
}