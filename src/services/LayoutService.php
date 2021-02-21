<?php 

namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\events\LayoutEvent;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use Ryssbowh\CraftThemes\models\Layout;
use Ryssbowh\CraftThemes\models\PageLayout;
use Ryssbowh\CraftThemes\records\LayoutRecord;
use craft\events\ConfigEvent;
use craft\helpers\StringHelper;

class LayoutService extends Service
{
    const EVENT_BEFORE_SAVE = 1;
    const EVENT_AFTER_SAVE = 2;
    const EVENT_BEFORE_APPLY_DELETE = 3;
    const EVENT_AFTER_DELETE = 4;
    const EVENT_BEFORE_DELETE = 5;
    const CONFIG_KEY = 'themes.layouts';

    /**
     * @var array
     */
    protected $layouts;

    /**
     * @var array
     */
    protected $available;

    /**
     * Get all layouts
     * 
     * @return array
     */
    public function getAll(): array
    {
        if ($this->layouts === null) {
            $this->layouts = array_map(function ($record) {
                return $record->toModel();
            }, LayoutRecord::find()->all());
        }
        return $this->layouts;
    }

    /**
     * Get all alyouts for a theme
     * 
     * @param  string       $theme
     * @param  bool|boolean $includeDefault
     * @return array
     */
    public function getAllForTheme(string $theme, bool $includeDefault = true): array
    {
        return array_filter($this->getAll(), function ($layout) use ($theme, $includeDefault) {
            if (!$includeDefault and $layout->type == 'default') {
                return false;
            }
            return $layout->theme == $theme;
        });
    }

    /**
     * Get all layouts indexed by theme's handle
     * 
     * @param  bool $asArrays
     * @return array
     */
    public function allIndexedByTheme(bool $asArrays = false): array
    {
        $layouts = [];
        foreach (Themes::$plugin->registry->getAll() as $theme) {
            $elems = $this->getAllForTheme($theme->handle, false);
            usort($elems, function ($elem, $elem2) {
                return strcasecmp($elem->description, $elem2->description);
            });
            array_unshift($elems, $this->getDefault($theme->handle));
            if ($asArrays) {
                $elems = array_map(function ($layout) {
                    return $layout->toArray();
                }, $elems);
            }
            $layouts[$theme->handle] = $elems;
        }
        return $layouts;
    }

    /**
     * Get all available layouts : entry sections, category groups and config routes
     * 
     * @param  bool $asArrays
     * @return array
     */
    public function getAvailable(bool $asArrays = false): array
    {
        if ($this->available === null) {
            $layouts = [];
            $groups = \Craft::$app->categories->getAllGroups();
            foreach ($groups as $group) {
                $layouts[] = Layout::create([
                    'type' => 'category',
                    'element' => $group->uid
                ]);
            }
            $sections = \Craft::$app->sections->getAllSections();
            foreach ($sections as $section) {
                $layouts[] = Layout::create([
                    'type' => 'entry',
                    'element' => $section->uid
                ]);
            }
            $routes = \Craft::$app->routes->getProjectConfigRoutes();
            foreach ($routes as $index => $route) {
                $layouts[] = Layout::create([
                    'type' => 'route',
                    'element' => $index
                ]);
            }
            $this->available = $layouts;
        }
        if ($asArrays) {
            return array_map(function ($layout) {
                return $layout->toArray();
            }, $this->available);
        }
        return $this->available;
    }

    /**
     * Get default layout for a theme
     * 
     * @param  string  $theme
     * @param  bool    $create
     * @return ?Layout
     */
    public function getDefault(string $theme, bool $create = true): ?Layout
    {
        $layout = $this->get($theme);
        if (!$layout && $create) {
            $layout = Layout::create([
                'type' => 'default',
                'theme' => $theme,
                'element' => ''
            ]);
            $this->save($layout);
            $this->layouts[] = $layout;
        }
        return $layout;
    }

    /**
     * Get layout by id
     * 
     * @param  int    $id
     * @return Layout
     * @throws LayoutException
     */
    public function getById(int $id): Layout
    {
        foreach ($this->getAll() as $layout) {
            if ($layout->id == $id) {
                return $layout;
            }
        }
        throw LayoutException::noId($id);
    }

    /**
     * Get a layout
     * 
     * @param  string  $theme
     * @param  string  $type
     * @param  string  $element
     * @param  boolean $load
     * @return ?Layout
     */
    public function get(string $theme, string $type = 'default', string $element = '', $load = false): ?Layout
    {
        foreach ($this->getAll() as $layout) {
            if ($layout->type == $type and $layout->theme == $theme and $layout->element == $element) {
                return $load ? $layout->loadBlocks() : $layout;
            }
        }
        return null;
    }

    /**
     * Save a layout
     * 
     * @param  Layout $layout
     * @return Layout
     */
    public function save(Layout $layout): Layout
    {
        $isNew = !is_int($layout->id);
        $uid = $isNew ? StringHelper::UUID() : $layout->uid;

        $this->triggerEvent(self::EVENT_BEFORE_SAVE, new LayoutEvent([
            'layout' => $layout
        ]));

        $projectConfig = \Craft::$app->getProjectConfig();
        $configData = $layout->getConfig();
        $configPath = self::CONFIG_KEY . '.' . $uid;
        $projectConfig->set($configPath, $configData);

        $record = $this->getRecordByUid($uid);
        $record->save(false);
        
        if ($isNew) {
            $layout->setAttributes($record->getAttributes(), false);
        }

        return $layout;
    }

    /**
     * Get layout record by uid, or a new one if it's not found
     * 
     * @param  string $uid
     * @return LayoutRecord
     */
    public function getRecordByUid(string $uid): LayoutRecord
    {
        return LayoutRecord::findOne(['uid' => $uid]) ?? new LayoutRecord;
    }

    /**
     * Deletes a layout
     * 
     * @param  Layout $layout
     * @return bool
     * @throws LayoutException
     */
    public function delete(Layout $layout): bool
    {
        if ($layout->type == 'default') {
            throw LayoutException::defaultUndeletable();
        }
        $this->triggerEvent(self::EVENT_BEFORE_DELETE, new LayoutEvent([
            'layout' => $layout
        ]));
        \Craft::$app->getProjectConfig()->remove(self::CONFIG_KEY . '.' . $layout->uid);
        return true;
    }

    /**
     * Handles a change in layout config
     * 
     * @param  ConfigEvent $event
     */
    public function handleChanged(ConfigEvent $event)
    {
        $uid = $event->tokenMatches[0];
        $data = $event->newValue;
        $transaction = \Craft::$app->getDb()->beginTransaction();

        try {
            $layout = $this->getRecordByUid($uid);
            $isNew = $layout->getIsNewRecord();

            $layout->uid = $uid;
            $layout->type = $data['type'];
            $layout->element = $data['element'];
            $layout->theme = $data['theme'];
            
            $layout->save(false);
            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }

        $this->triggerEvent(self::EVENT_AFTER_SAVE, new LayoutEvent([
            'layout' => $layout,
            'isNew' => $isNew,
        ]));
    }

    /**
     * Handles a deletion in layout config
     * 
     * @param  ConfigEvent $event
     */
    public function handleDeleted(ConfigEvent $event)
    {
        $uid = $event->tokenMatches[0];
        $layout = $this->getRecordByUid($uid);

        if (!$layout) {
            return;
        }

        $this->triggerEvent(self::EVENT_BEFORE_APPLY_DELETE, new LayoutEvent([
            'layout' => $layout
        ]));

        \Craft::$app->getDb()->createCommand()
            ->delete(LayoutRecord::tableName(), ['uid' => $uid])
            ->execute();

        $this->triggerEvent(self::EVENT_AFTER_DELETE, new LayoutEvent([
            'layout' => $layout
        ]));
    }
}