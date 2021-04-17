<?php 

namespace Ryssbowh\CraftThemes\services;

use Illuminate\Support\Collection;
use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\events\LayoutEvent;
use Ryssbowh\CraftThemes\exceptions\LayoutException;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use Ryssbowh\CraftThemes\models\PageLayout;
use Ryssbowh\CraftThemes\models\layouts\CategoryLayout;
use Ryssbowh\CraftThemes\models\layouts\EntryLayout;
use Ryssbowh\CraftThemes\models\layouts\GlobalLayout;
use Ryssbowh\CraftThemes\models\layouts\Layout;
use Ryssbowh\CraftThemes\models\layouts\RouteLayout;
use Ryssbowh\CraftThemes\models\layouts\TagLayout;
use Ryssbowh\CraftThemes\models\layouts\UserLayout;
use Ryssbowh\CraftThemes\models\layouts\VolumeLayout;
use Ryssbowh\CraftThemes\records\BlockRecord;
use Ryssbowh\CraftThemes\records\LayoutRecord;
use Ryssbowh\CraftThemes\records\ViewModeRecord;
use craft\db\ActiveRecord;
use craft\elements\Category;
use craft\elements\Entry;
use craft\events\ConfigEvent;
use craft\events\EntryTypeEvent;
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
    const USER_HANDLE = 'user';
    const VOLUME_HANDLE = 'volume';
    const GLOBAL_HANDLE = 'global';
    const TAG_HANDLE = 'tag';
    const TYPES = [self::DEFAULT_HANDLE, self::CATEGORY_HANDLE, self::ENTRY_HANDLE, self::ROUTE_HANDLE, self::USER_HANDLE, self::VOLUME_HANDLE, self::GLOBAL_HANDLE, self::TAG_HANDLE];

    /**
     * @var Collection
     */
    protected $_layouts;

    /**
     * @var Layout
     */
    protected $current;

    public function all()
    {
        if ($this->_layouts === null) {
            $records = LayoutRecord::find()->with(['blocks', 'viewModes'])->all();
            $this->_layouts = collect();
            foreach ($records as $record) {
                $this->_layouts->push($this->create($record));
            }
        }
        return $this->_layouts;
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
        if ($layout = $this->all()->firstWhere('id', $id)) {
            return $layout;
        }
        throw LayoutException::noId($id);
    }

    public function withDisplays(): array
    {
        return $this->all()->filter(function ($layout) {
            return $layout->hasDisplays();
        })->all();
    }

    /**
     * Create a layout
     * 
     * @param  array  $args
     * @return Layout
     * @throws LayoutException
     */
    public function create($config): Layout
    {
        if ($config instanceof ActiveRecord) {
            $config = $config->getAttributes();
        }
        $viewModesData = $config['viewModes'] ?? null;
        switch ($config['type']) {
            case self::DEFAULT_HANDLE:
                $layout = new Layout;
                break;
            case self::ROUTE_HANDLE:
                $layout = new RouteLayout;
                break;
            case self::CATEGORY_HANDLE:
                $layout = new CategoryLayout;
                break;
            case self::ENTRY_HANDLE:
                $layout = new EntryLayout;
                break;
            case self::USER_HANDLE:
                $layout = new UserLayout;
                break;
            case self::VOLUME_HANDLE:
                $layout = new VolumeLayout;
                break;
            case self::GLOBAL_HANDLE:
                $layout = new GlobalLayout;
                break;
            case self::TAG_HANDLE:
                $layout = new TagLayout;
                break;
            default:
                throw LayoutException::unknownType($config['type']);
        }
        $attributes = $layout->safeAttributes();
        $config = array_intersect_key($config, array_flip($attributes));
        $layout->setAttributes($config);
        if ($viewModesData) {
            foreach ($viewModesData as $data) {
                $viewMode = $this->viewModesService()->create($data);
                $viewModes[] = $viewMode;
            }
            $layout->viewModes = $viewModes;
        }
        return $layout;
    }

    /**
     * Get all layouts with blocks indexed by theme's handle
     * 
     * @return array
     */
    public function getBlockLayouts(): array
    {
        $layouts = [];
        foreach (Themes::$plugin->registry->getAll() as $theme) {
            $layouts[$theme->handle] = $this->all()->filter(function ($layout) use ($theme) {
                return ($layout->canHaveUrls() and $layout->theme == $theme->handle);
            })->sort(function ($elem, $elem2) {
                return strcasecmp($elem->description, $elem2->description);
            })->map(function ($layout) {
                return $layout->toArray();
            })->values()->all();
        }
        return $layouts;
    }

    /**
     * Get all layouts with displays indexed by theme's handle
     * 
     * @return array
     */
    public function getDisplayLayouts(): array
    {
        $layouts = [];
        foreach (Themes::$plugin->registry->getAll() as $theme) {
            $elems = 
            $layouts[$theme->handle] = $this->all()->filter(function ($layout) use ($theme) {
                return ($layout->hasDisplays() and $layout->theme == $theme->handle);
            })->sort(function ($elem, $elem2) {
                return strcasecmp($elem->description, $elem2->description);
            })->map(function ($layout) {
                return $layout->toArray();
            })->values()->all();
        }
        return $layouts;
    }

    /**
     * Create all layouts and deletes non existing
     */
    public function install()
    {
        $ids = [];
        foreach ($this->themesRegistry()->getNonPartials() as $theme) {
            foreach ($this->getAvailable() as $layout) {
                if (!$layout2 = $this->get($theme->handle, $layout->type, $layout->element)) {
                    $layout->theme = $theme->handle;
                    $layout2 = $layout;
                }
                if (!$layout->viewModes) {
                    $viewMode = $this->viewModesService()->create([
                        'handle' => ViewModeService::DEFAULT_HANDLE,
                        'name' => \Craft::t('themes', 'Default'),
                        'layout_id' => $layout2->id
                    ]);
                    $layout->viewModes = [$viewMode];
                }
                $this->save($layout2);
                $ids[] = $layout2->id;
            }
        }
        $layouts = $this->all()->whereNotIn('id', $ids)->all();
        foreach ($layouts as $layout) {
            $this->delete($layout, true);
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
        return $this->get($theme, self::DEFAULT_HANDLE);
    }

    /**
     * Get all available layouts : default, entry sections, category groups and config routes
     * 
     * @return array
     */
    public function getAvailable(): array
    {
        return [
            $this->create([
                'type' => self::DEFAULT_HANDLE,
                'element' => '',
                'hasBlocks' => true
            ]),
            $this->create([
                'type' => self::USER_HANDLE,
                'element' => ''
            ]),
            ...$this->getCategoryLayouts(),
            ...$this->getEntryLayouts(),
            ...$this->getRouteLayouts(),
            ...$this->getVolumesLayouts(),
            ...$this->getGlobalsLayouts(),
            ...$this->getTagsLayouts()
        ];
    }

    /**
     * Get a layout
     * 
     * @param  string  $theme
     * @param  string  $element
     * @param  string  $type
     * @param  boolean $load
     * @return ?Layout
     */
    public function get(string $theme, string $type, string $element = '', bool $load = false): ?Layout
    {
        $layout = $this->all()
            ->where('theme', $theme)
            ->where('element', $element)
            ->firstWhere('type', $type);
        if ($layout and $load) {
            return $layout->loadBlocks();
        }
        return $layout;
    }

    /**
     * Save a layout
     * 
     * @param  Layout $layout
     * @return Layout
     */
    public function save(Layout $layout): bool
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
        $layout->setAttributes($record->getAttributes(), false);
        $layout->viewModes = null;
        $layout->blocks = null;
        
        if ($isNew) {
            $this->all()->push($layout);
        }

        return true;
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
        if (!$force and $layout->type == self::DEFAULT_HANDLE) {
            throw LayoutException::defaultUndeletable();
        }
        $this->triggerEvent(self::EVENT_BEFORE_DELETE, new LayoutEvent([
            'layout' => $layout
        ]));

        foreach ($this->displayService()->getForLayout($layout) as $display) {
            $this->displayService()->delete($display);
        }
        foreach ($this->blocksService()->getForLayout($layout) as $block) {
            $this->blocksService()->delete($block);
        }

        \Craft::$app->getProjectConfig()->remove(self::CONFIG_KEY . '.' . $layout->uid);

        // $this->blockService()->deleteLayoutBlocks($layout);

        $this->_layouts = $this->all()->where('id', '!=', $layout->id);

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

            // Saving view modes
            $ids = [];
            foreach ($data['viewModes'] as $viewModesData) {
                $viewMode = $this->viewModesService()->getRecordByUid($viewModesData['uid']);
                $viewMode->uid = $viewModesData['uid'];
                $viewMode->handle = $viewModesData['handle'];
                $viewMode->name = $viewModesData['name'];
                $viewMode->layout_id = $layout->id;
                $viewMode->save(false);
                $ids[] = $viewMode->id;
            }
            $toDelete = ViewModeRecord::find()
                ->where(['layout_id' => $layout->id])
                ->andWhere(['not in', 'id', $ids])
                ->all();
            foreach ($toDelete as $viewMode) {
                $viewMode->delete();
            }

            // Saving blocks
            $ids = [];
            foreach ($data['blocks'] ?? [] as $blockData) {
                $block = $this->blocksService()->getRecordByUid($blockData['uid']);
                $block->uid = $blockData['uid'];
                $block->provider = $blockData['provider'];
                $block->region = $blockData['region'];
                $block->handle = $blockData['handle'];
                $block->order = $blockData['order'];
                $block->options = $blockData['options'] ?? null;
                $block->layout_id = $layout->id;
                $block->save(false);
                $ids[] = $block->id;
            }
            $toDelete = BlockRecord::find()
                ->where(['layout_id' => $layout->id])
                ->andWhere(['not in', 'id', $ids])
                ->all();
            foreach ($toDelete as $block) {
                $block->delete();
            }
            
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
     * Creates a entry type or category layout displays
     * 
     * @param EntryTypeEvent $event
     */
    public function onCraftElementDeleted(string $type, string $uid)
    {
        $layouts = $this->all()->filter(function ($layout) use ($uid) {
            return $layout->element == $uid;
        });
        foreach ($layouts as $layout) {
            $this->delete($layout, true);
        }
    }

    /**
     * Creates a entry type layout
     * 
     * @param ConfigEvent $event
     */
    public function onCraftElementSaved(string $type, string $uid)
    {
        foreach ($this->themesRegistry()->getNonPartials() as $theme) {
            if (!$this->get($theme->handle, $type, $uid)) {
                $layout = $this->create([
                    'type' => $type,
                    'element' => $uid,
                    'theme' => $theme->handle,
                    'viewModes' => [
                        [
                            'handle' => ViewModeService::DEFAULT_HANDLE,
                            'name' => \Craft::t('themes', 'Default'),
                        ]
                    ]
                ]);
                $this->save($layout);
            }
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
            $layout = $this->create([
                'type' => self::CATEGORY_HANDLE,
                'element' => $uid,
                'theme' => $theme->handle,
                'viewModes' => [
                    [
                        'handle' => ViewModeService::DEFAULT_HANDLE,
                        'name' => \Craft::t('themes', 'Default'),
                    ]
                ]
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
            $layout = $this->create([
                'type' => self::ROUTE_HANDLE,
                'element' => $uid,
                'theme' => $theme->handle,
                'viewModes' => [
                    [
                        'handle' => ViewModeService::DEFAULT_HANDLE,
                        'name' => \Craft::t('themes', 'Default'),
                    ]
                ]
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
            $layout = $this->get($theme, self::CATEGORY_HANDLE, $element->getGroup()->uid);
        } elseif ($element instanceof Entry) {
            $layout = $this->get($theme, self::ENTRY_HANDLE, $element->getType()->uid);
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
        $groups = \Craft::$app->categories->getAllGroups();
        $layouts = [];
        foreach ($groups as $group) {
            $layouts[] = $this->create([
                'type' => self::CATEGORY_HANDLE,
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
        $sections = \Craft::$app->sections->getAllSections();
        $layouts = [];
        foreach ($sections as $section) {
            foreach ($section->getEntryTypes() as $entryType) {
                $layouts[] = $this->create([
                    'type' => self::ENTRY_HANDLE,
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
        $routes = \Craft::$app->projectConfig->get(Routes::CONFIG_ROUTES_KEY);
        $layouts = [];
        foreach ($routes as $uid => $route) {
            $layouts[] = $this->create([
                'type' => self::ROUTE_HANDLE,
                'element' => $uid
            ]);
        }
        return $layouts;
    }

    protected function getVolumesLayouts(): array
    {
        $volumes = \Craft::$app->volumes->getAllVolumes();
        $layouts = [];
        foreach ($volumes as $volume) {
            $layouts[] = $this->create([
                'type' => self::VOLUME_HANDLE,
                'element' => $volume->uid
            ]);
        }
        return $layouts;
    }

    protected function getGlobalsLayouts(): array
    {
        $sets = \Craft::$app->globals->getAllSets();
        $layouts = [];
        foreach ($sets as $set) {
            $layouts[] = $this->create([
                'type' => self::GLOBAL_HANDLE,
                'element' => $set->uid
            ]);
        }
        return $layouts;
    }

    protected function getTagsLayouts(): array
    {
        $groups = \Craft::$app->tags->getAllTagGroups();
        $layouts = [];
        foreach ($groups as $group) {
            $layouts[] = $this->create([
                'type' => self::TAG_HANDLE,
                'element' => $group->uid
            ]);
        }
        return $layouts;
    }
}