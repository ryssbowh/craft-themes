<?php 

namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\events\ViewModeEvent;
use Ryssbowh\CraftThemes\models\ViewMode;
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
            $this->createDefaults();
        }
        return $this->viewModes;
    }

    /**
     * Get all view modes for a layout
     * 
     * @param  string $theme
     * @param  string $layout
     * @return array
     */
    public function forLayout(string $theme, string $layout): array
    {
        return array_values(array_filter($this->forTheme($theme), function ($viewMode) use ($layout) {
            return ($viewMode->layout == $layout);
        }));
    }

    /**
     * Get a default view mode
     * 
     * @param  string $theme
     * @param  string $layout
     * @return array
     */
    public function getDefault(string $theme, string $layout): ?ViewMode
    {
        return $this->get($theme, $layout, 'default');
    }

    /**
     * Get a view mode
     * 
     * @param  string $theme
     * @param  string $layout
     * @param  string $handle
     * @return array
     */
    public function get(string $theme, string $layout, string $handle): ?ViewMode
    {
        foreach ($this->forLayout($theme, $layout) as $viewMode) {
            if ($viewMode->handle == $handle) {
                return $viewMode;
            }
        }
        return null;
    }

    /**
     * Get all view modes for a theme
     * 
     * @param  string $theme
     * @return array
     */
    public  function forTheme(string $theme): array
    {
        return array_values(array_filter($this->getAll(), function ($viewMode) use ($theme) {
            return ($viewMode->theme == $theme);
        }));
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
        if ($viewMode->handle == 'default') {
            throw ViewModeException::defaultUndeletable();
        }
        $this->triggerEvent(self::EVENT_BEFORE_DELETE, new ViewModeEvent([
            'viewMode' => $viewMode
        ]));
        \Craft::$app->getProjectConfig()->remove(self::CONFIG_KEY . '.' . $viewMode->uid);
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
            $viewMode->theme = $data['theme'];
            $viewMode->layout = $data['layout'];
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
     * Creates defaults view modes for all themes/layouts
     */
    protected function createDefaults()
    {
        foreach ($this->themesRegistry()->getNonPartials() as $theme) {
            foreach ($this->layoutService()->getAvailable() as $layout) {
                if (!$this->getDefault($theme->handle, $layout->handle)) {
                    $this->createDefault($theme->handle, $layout->handle);
                }
            }
        }
    }

    /**
     * Creates the default view mode for a theme and a layout
     * 
     * @param  string $theme
     * @param  string $layout
     * @return ViewMode
     */
    protected function createDefault(string $theme, string $layout): ViewMode
    {
        $viewMode = new ViewMode([
            'handle' => 'default',
            'name' => \Craft::t('themes', 'Default'),
            'layout' => $layout,
            'theme' => $theme
        ]);
        $this->save($viewMode);
        $viewModes = $this->viewModes;
        array_unshift($viewModes, $viewMode);
        $this->viewModes = $viewModes;
        return $viewMode;
    }
}