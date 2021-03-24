<?php 

namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\events\LayoutEvent;
use Ryssbowh\CraftThemes\exceptions\LayoutException;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use Ryssbowh\CraftThemes\models\PageLayout;
use Ryssbowh\CraftThemes\models\layouts\Layout;
use Ryssbowh\CraftThemes\records\LayoutRecord;
use craft\elements\Category;
use craft\elements\Entry;
use craft\events\ConfigEvent;
use craft\helpers\StringHelper;
use craft\services\Routes;

class LayoutService extends Service
{
    const EVENT_BEFORE_SAVE = 1;
    const EVENT_AFTER_SAVE = 2;
    const EVENT_BEFORE_APPLY_DELETE = 3;
    const EVENT_AFTER_DELETE = 4;
    const EVENT_BEFORE_DELETE = 5;
    const CONFIG_KEY = 'themes.layouts';

    const DEFAULT_HANDLE = 'default';
    const CATEGORY_HANDLE = 'category';
    const ENTRY_HANDLE = 'entry';
    const ROUTE_HANDLE = 'route';

    /**
     * @var array
     */
    protected $layouts;

    /**
     * @var array
     */
    protected $available;

    /**
     * @var Layout
     */
    protected $current;

    /**
     * Get all layouts
     *
     * @param  bool $onlyHasFields
     * @return array
     */
    public function getAll(bool $onlyHasDisplays = false): array
    {
        if ($this->layouts === null) {
            $this->layouts = array_map(function ($record) {
                return $record->toModel();
            }, LayoutRecord::find()->with(['blocks', 'viewModes'])->all());
        }
        return array_filter($this->layouts, function ($layout) use ($onlyHasDisplays) {
            if ($onlyHasDisplays and !$layout->hasDisplays) {
                return false;
            }
            return true;
        });
    }

    /**
     * Get all layouts with blocks indexed by theme's handle
     * 
     * @param  bool $asArrays
     * @param  bool $onlyHasFields return only layouts that can define fields
     * @param  bool $withDefault include default layout
     * @return array
     */
    public function getLayoutsByTheme(bool $asArrays = false, bool $onlyHasDisplays = false, bool $withDefault = true): array
    {
        $layouts = [];
        foreach (Themes::$plugin->registry->getAll() as $theme) {
            $elems = array_filter($this->getAll(), function ($layout) use ($theme, $onlyHasDisplays) {
                if ($layout->type == LayoutService::DEFAULT_HANDLE or ($onlyHasDisplays && !$layout->hasDisplays)) {
                    return false;
                }
                return $layout->theme == $theme->handle;
            });
            usort($elems, function ($elem, $elem2) {
                return strcasecmp($elem->description, $elem2->description);
            });
            if ($withDefault and $default = $this->getDefault($theme->handle)) {
                array_unshift($elems, $default);
            }
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
     * Create all layouts and deletes non existing
     */
    public function createAll()
    {
        $ids = [];
        foreach ($this->themesRegistry()->getNonPartials() as $theme) {
            foreach ($this->getAvailable() as $layout) {
                if (!$layout2 = $this->get($theme->handle, $layout->element)) {
                    $layout->id = null;
                    $layout->theme = $theme->handle;
                    $this->save($layout);
                    $ids[] = $layout->id;
                    $layout2 = $layout;
                } else {
                    $ids[] = $layout2->id;
                }
                if (!$this->viewModeService()->get($layout2)) {
                    $this->viewModeService()->create($layout2);
                }
            }
        }
        $layouts = LayoutRecord::find()->where(['not in', 'id', $ids])->all();
        foreach ($layouts as $layout) {
            $this->delete($layout->toModel(), true);
        }
    }

    /**
     * Get default layout for a theme
     * 
     * @param  string  $theme
     * @return ?Layout
     */
    public function getDefault(string $theme): ?Layout
    {
        return $this->get($theme);
    }

    /**
     * Get all available layouts : default, entry sections, category groups and config routes
     * 
     * @return array
     */
    public function getAvailable(bool $withDefault = true): array
    {
        if ($this->available === null) {
            $this->available = $withDefault ? [Layout::create([
                'type' => LayoutService::DEFAULT_HANDLE,
                'element' => '',
                'hasBlocks' => true
            ])] : [];
            
            $this->available = array_merge(
                $this->available,
                $this->getCategoryLayouts(),
                $this->getEntryLayouts(),
                $this->getRouteLayouts()
            );
        }
        return $this->available;
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
     * @param  string  $element
     * @param  boolean $load
     * @return ?Layout
     */
    public function get(string $theme, string $element = '', bool $load = false): ?Layout
    {
        foreach ($this->getAll() as $layout) {
            if ($layout->theme == $theme and $layout->element == $element) {
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

        $this->layouts[] = $layout;
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
     * @param  bool $force
     * @return bool
     * @throws LayoutException
     */
    public function delete(Layout $layout, bool $force = false): bool
    {
        if (!$force and $layout->type == LayoutService::DEFAULT_HANDLE) {
            throw LayoutException::defaultUndeletable();
        }
        $this->triggerEvent(self::EVENT_BEFORE_DELETE, new LayoutEvent([
            'layout' => $layout
        ]));
        \Craft::$app->getProjectConfig()->remove(self::CONFIG_KEY . '.' . $layout->uid);

        $this->blockService()->deleteLayoutBlocks($layout);

        foreach ($this->getAll() as $index => $layout2) {
            if ($layout->id == $layout2->id) {
                unset($this->layouts[$index]);
                break;
            }
        }

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
            $layout->hasBlocks = $data['hasBlocks'];
            
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

    /**
     * Delete layout associated to an element (route, category or entry type uid)
     * 
     * @param ConfigEvent $event
     */
    public function onCraftElementDeleted(ConfigEvent $event)
    {
        $uid = $event->tokenMatches[0];
        $layouts = array_filter($this->getAll(), function ($layout) use ($uid) {
            return $layout->element == $uid;
        });
        foreach ($layouts as $layout) {
            $this->delete($layout);
        }
    }

    /**
     * Creates a entry type layout
     * 
     * @param ConfigEvent $event
     */
    public function onEntryTypeAdded(ConfigEvent $event)
    {
        $uid = $event->tokenMatches[0];
        foreach ($this->themesRegistry()->getNonPartials() as $theme) {
            $layout = Layout::create([
                'type' => LayoutService::ENTRY_HANDLE,
                'element' => $uid,
                'theme' => $theme->handle
            ]);
            $this->save($layout);
        }
    }

    /**
     * Creates a category type layout
     * 
     * @param ConfigEvent $event
     */
    public function onCategoryAdded(ConfigEvent $event)
    {
        $uid = $event->tokenMatches[0];
        foreach ($this->themesRegistry()->getNonPartials() as $theme) {
            $layout = Layout::create([
                'type' => LayoutService::CATEGORY_HANDLE,
                'element' => $uid,
                'theme' => $theme->handle
            ]);
            $this->save($layout);
        }
    }

    /**
     * Creates a route type layout
     * 
     * @param ConfigEvent $event
     */
    public function onRouteAdded(ConfigEvent $event)
    {
        $uid = $event->tokenMatches[0];
        foreach ($this->themesRegistry()->getNonPartials() as $theme) {
            $layout = Layout::create([
                'type' => LayoutService::ROUTE_HANDLE,
                'element' => $uid,
                'theme' => $theme->handle
            ]);
            $this->save($layout);
        }
    }

    /**
     * Resolve current layout.
     * 
     * @param  string         $theme
     * @param  Entry|Category $element
     * @return ?Layout
     */
    public function resolveForRequest(string $theme, $element): ?Layout
    {
        $layout = null;
        if ($element instanceof Category) {
            $layout = $this->get($theme, $element->getGroup()->uid);
        } elseif ($element instanceof Entry) {
            $layout = $this->get($theme, $element->getType()->uid);
        }
        if ($layout) {
            $layout->loadBlocks();
        }
        $this->current = $layout;
        return $layout;
    }

    public function getCurrent(): ?Layout
    {
        return $this->current;
    }

    /**
     * Load all layouts for categories
     */
    protected function getCategoryLayouts(): array
    {
        $this->available[LayoutService::CATEGORY_HANDLE] = [];
        $groups = \Craft::$app->categories->getAllGroups();
        $layouts = [];
        foreach ($groups as $group) {
            $layouts[] = Layout::create([
                'type' => LayoutService::CATEGORY_HANDLE,
                'element' => $group->uid
            ]);
        }
        return $layouts;
    }

    /**
     * Load all layouts for entries
     */
    protected function getEntryLayouts(): array
    {
        $this->available[LayoutService::ENTRY_HANDLE] = [];
        $sections = \Craft::$app->sections->getAllSections();
        $layouts = [];
        foreach ($sections as $section) {
            foreach ($section->getEntryTypes() as $entryType) {
                $layouts[] = Layout::create([
                    'type' => LayoutService::ENTRY_HANDLE,
                    'element' => $entryType->uid
                ]);
            }
        }
        return $layouts;
    }

    /**
     * Load all layouts for routes
     */
    protected function getRouteLayouts(): array
    {
        $this->available[LayoutService::ROUTE_HANDLE] = [];
        $routes = \Craft::$app->projectConfig->get(Routes::CONFIG_ROUTES_KEY);
        $layouts = [];
        foreach ($routes as $uid => $route) {
            $layouts[] = Layout::create([
                'type' => LayoutService::ROUTE_HANDLE,
                'element' => $uid
            ]);
        }
        return $layouts;
    }
}