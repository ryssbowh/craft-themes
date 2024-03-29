<?php
namespace Ryssbowh\CraftThemes\services;

use Illuminate\Support\Collection;
use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\events\AvailableLayoutsEvent;
use Ryssbowh\CraftThemes\events\LayoutEvent;
use Ryssbowh\CraftThemes\events\RegisterLayoutTypesEvent;
use Ryssbowh\CraftThemes\events\ResolveRequestLayoutEvent;
use Ryssbowh\CraftThemes\exceptions\LayoutException;
use Ryssbowh\CraftThemes\exceptions\ThemeException;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use Ryssbowh\CraftThemes\models\ViewMode;
use Ryssbowh\CraftThemes\models\layouts\CategoryLayout;
use Ryssbowh\CraftThemes\models\layouts\CustomLayout;
use Ryssbowh\CraftThemes\models\layouts\EntryLayout;
use Ryssbowh\CraftThemes\models\layouts\GlobalLayout;
use Ryssbowh\CraftThemes\models\layouts\Layout;
use Ryssbowh\CraftThemes\models\layouts\ProductLayout;
use Ryssbowh\CraftThemes\models\layouts\TagLayout;
use Ryssbowh\CraftThemes\models\layouts\UserLayout;
use Ryssbowh\CraftThemes\models\layouts\VariantLayout;
use Ryssbowh\CraftThemes\models\layouts\VolumeLayout;
use Ryssbowh\CraftThemes\records\LayoutRecord;
use Ryssbowh\CraftThemes\services\ViewModeService;
use craft\base\Element;
use craft\commerce\Plugin as Commerce;
use craft\commerce\elements\Product;
use craft\elements\Category;
use craft\elements\Entry;
use craft\events\ConfigEvent;
use craft\events\EntryTypeEvent;
use craft\events\RebuildConfigEvent;
use craft\helpers\StringHelper;

class LayoutService extends Service
{
    const EVENT_BEFORE_SAVE = 'before_save';
    const EVENT_AFTER_SAVE = 'after_save';
    const EVENT_BEFORE_APPLY_DELETE = 'before_apply_delete';
    const EVENT_AFTER_DELETE = 'after_delete';
    const EVENT_BEFORE_DELETE = 'before_delete';
    const CONFIG_KEY = 'themes.layouts';
    
    /**
     * @since 3.1.0
     */
    const EVENT_REGISTER_TYPES = 'register_types';
    /**
     * @since 3.1.0
     */
    const EVENT_AVAILABLE_LAYOUTS = 'available_layouts';
    /**
     * @since 3.1.0
     */
    const EVENT_RESOLVE_REQUEST_LAYOUT = 'resolve_request_layout';

    /**
     * @var Collection
     */
    protected $_layouts;

    /**
     * @var array
     * @since 3.1.0
     */
    protected $_types;

    /**
     * Marker when layouts are being installed
     * @var boolean
     */
    public static $isInstalling = false;

    /**
     * All layouts getter
     * 
     * @return Collection
     */
    public function getAll(): Collection
    {
        if ($this->_layouts === null) {
            $records = LayoutRecord::find()->all();
            $this->_layouts = collect();
            foreach ($records as $record) {
                try {
                    $this->_layouts->push($this->create($record));
                } catch (LayoutException $e) {}
            }
        }
        return $this->_layouts;
    }

    /**
     * Create a layout from config
     * 
     * @param  array|ActiveRecord $config
     * @return LayoutInterface
     * @throws LayoutException
     */
    public function create($config): LayoutInterface
    {
        if ($config instanceof LayoutRecord) {
            $config = $config->getAttributes();
        }
        if (!isset($config['themeHandle'])) {
            throw LayoutException::parameterMissing('themeHandle', __METHOD__);
        }
        if ($viewModesData = $config['viewModes'] ?? null) {
            foreach ($viewModesData as $data) {
                $viewMode = $this->viewModesService()->create($data);
                $viewModes[] = $viewMode;
            }
            $config['viewModes'] = $viewModes;
        }
        if ($class = $this->types[$config['type']] ?? null) {
            $layout = new $class;
        } else {
            throw LayoutException::unknownType($config['type']);
        }
        $config = array_intersect_key($config, array_flip($layout->safeAttributes()));
        $layout->setAttributes($config);
        return $layout;
    }

    /**
     * Get defined layouts types
     * 
     * @return array
     * @since 3.1.0
     */
    public function getTypes(): array
    {
        if ($this->_types === null) {
            $event = new RegisterLayoutTypesEvent;
            $this->triggerEvent(self::EVENT_REGISTER_TYPES, $event);
            $this->_types = $event->types;
        }
        return $this->_types;
    }

    /**
     * Get the type of a layout
     * 
     * @param  LayoutInterface $layout
     * @return ?string
     * @since 3.1.0
     */
    public function getType(LayoutInterface $layout): ?string
    {
        foreach ($this->types as $name => $class) {
            if ($class == get_class($layout)) {
                return $name;
            }
        }
        return null;
    }

    /**
     * Get layout by id
     * 
     * @param  int    $id
     * @return LayoutInterface
     * @throws LayoutException
     */
    public function getById(int $id): LayoutInterface
    {
        if ($layout = $this->getAll()->firstWhere('id', $id)) {
            return $layout;
        }
        throw LayoutException::noId($id);
    }

    /**
     * Get layout by uid
     * 
     * @param  int    $id
     * @return LayoutInterface
     * @throws LayoutException
     */
    public function getByUid(string $uid): LayoutInterface
    {
        if ($layout = $this->getAll()->firstWhere('uid', $uid)) {
            return $layout;
        }
        throw LayoutException::noUid($uid);
    }

    /**
     * Get all layouts that have displays
     * 
     * @return LayoutInterface[]
     */
    public function withDisplays(): array
    {
        return $this->getAll()->filter(function ($layout) {
            return $layout->hasDisplays();
        })->values()->all();
    }

    /**
     * Copy a layout into custom one
     *
     * @param  LayoutInterface $layout
     * @param  string          $name
     * @param  string          $handle
     * @return LayoutInterface
     */
    public function copyIntoCustom(LayoutInterface $layout, string $name, string $handle): LayoutInterface
    {
        $blocks = [];
        foreach ($layout->blocks as $block) {
            $block = clone $block;
            $block->id = null;
            $block->uid = null;
            $blocks[] = $block;
        }
        $layout = $this->createCustom([
            'name' => $name,
            'elementUid' => $handle,
            'themeHandle' => $layout->themeHandle,
        ]);
        $layout->blocks = $blocks;
        $this->save($layout);
        return $layout;
    }

    /**
     * Get all layouts for a theme
     * 
     * @param  string|ThemeInterface $theme theme instance or theme handle
     * @param  bool|null $withHasDisplays
     * @param  bool|null $withHasBlocks
     * @return LayoutInterface[]
     */
    public function getForTheme($theme, ?bool $withHasDisplays = null, ?bool $withHasBlocks = null): array
    {
        $theme = $this->getThemeHandle($theme);
        return $this->getAll()->filter(function ($layout) use ($theme, $withHasDisplays, $withHasBlocks) {
            if ($layout->themeHandle != $theme) {
                return false;
            }
            if ($withHasDisplays !== null) {
                return $layout->hasDisplays() === $withHasDisplays;
            }
            if ($withHasBlocks !== null) {
                return (bool)$layout->hasBlocks === $withHasBlocks;
            }
            return true;
        })->values()->all();
    }

    /**
     * Get all layouts that can have blocks indexed by theme's handle
     * 
     * @return LayoutInterface[]
     */
    public function getBlockLayouts(): array
    {
        $layouts = [];
        foreach (Themes::$plugin->registry->getAll() as $theme) {
            $layouts[$theme->handle] = $this->getAll()->filter(function ($layout) use ($theme) {
                return ($layout->canHaveBlocks() and $layout->themeHandle == $theme->handle);
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
     * @return LayoutInterface[]
     */
    public function getWithDisplays(): array
    {
        $layouts = [];
        foreach (Themes::$plugin->registry->getAll() as $theme) {
            $layouts[$theme->handle] = $this->getAll()->filter(function ($layout) use ($theme) {
                return ($layout->hasDisplays() and $layout->themeHandle == $theme->handle);
            })->sort(function ($elem, $elem2) {
                return strcasecmp($elem->description, $elem2->description);
            })->map(function ($layout) {
                return $layout->toArray();
            })->values()->all();
        }
        return $layouts;
    }

    /**
     * (Re)create all layouts for all themes.
     */
    public function installAll()
    {
        foreach ($this->themesRegistry()->getNonPartials() as $theme) {
            $this->installForTheme($theme);
        }
    }

    /**
     * (Re)create layouts for a theme, will deletes orphans.
     * 
     * @param  ThemeInterface $theme
     * @return bool
     */
    public function installForTheme(ThemeInterface $theme): bool
    {
        if ($theme->isPartial() or !Themes::$plugin->is(Themes::EDITION_PRO)) {
            return false;
        }
        static::$isInstalling = true;
        $ids = [];
        foreach ($this->getAvailable($theme->handle) as $layout) {
            $layout->themeHandle = $theme->handle;
            if ($exists = $this->get($theme, $layout->type, $layout->elementUid)) {
                $layout->id = $exists->id;
                $layout->uid = $exists->uid;
            }
            $this->installLayoutData($layout);
            $ids[] = $layout->id;
        }
        $layouts = $this->getAll()
            ->whereNotIn('id', $ids)
            ->where('themeHandle', $theme->handle)
            ->all();
        foreach ($layouts as $layout) {
            $this->delete($layout, true);
        }
        static::$isInstalling = false;
        return true;
    }

    /**
     * Deletes all layouts for a theme. 
     * 
     * @param  ThemeInterface $theme
     * @return bool
     */
    public function uninstallForTheme(ThemeInterface $theme): bool
    {
        foreach ($this->getForTheme($theme) as $layout) {
            $this->delete($layout, true);
        }
        return true;
    }

    /**
     * Get default layout for a theme
     * 
     * @param  string|ThemeInterface  $theme theme instance or theme handle
     * @return ?LayoutInterface
     */
    public function getDefault($theme): ?LayoutInterface
    {
        return $this->get($theme, 'default');
    }

    /**
     * Get a layout
     * 
     * @param  string|ThemeInterface $theme theme instance or theme handle
     * @param  string                $elementUid
     * @param  string                $type
     * @param  boolean               $loadBlocks
     * @return ?LayoutInterface
     */
    public function get($theme, string $type, string $elementUid = ''): ?LayoutInterface
    {
        return $this->getAll()
            ->where('themeHandle', $this->getThemeHandle($theme))
            ->where('elementUid', $elementUid)
            ->firstWhere('type', $type);
    }

    /**
     * Returns all layouts for a type
     * 
     * @param  string|ThemeInterface $theme theme instance or theme handle
     * @param  string                $type
     * @return LayoutInterface[]
     */
    public function getForType($theme, string $type): array
    {
        $theme = $this->getThemeHandle($theme);
        return $this->getAll()
            ->where('themeHandle', $theme)
            ->where('type', $type)
            ->values()
            ->all();
    }

    /**
     * Get a custom layout by handle
     * 
     * @param  string|ThemeInterface $theme theme instance or theme handle
     * @param  string                $handle
     * @return ?CustomLayout
     */
    public function getCustom($theme, string $handle): ?CustomLayout
    {
        return $this->get($theme, 'custom', $handle);
    }

    /**
     * Save a layout
     * 
     * @param  LayoutInterface $layout
     * @param  bool $validate
     * @throws LayoutException
     * @return bool
     */
    public function save(LayoutInterface $layout, bool $validate = true): bool
    {
        if ($validate and !$layout->validate()) {
            return false;
        }

        $isNew = !is_int($layout->id);

        $existing = $this->get($layout->theme, $layout->type, $layout->elementUid ?? '');
        if ($existing and $existing->id != $layout->id) {
            throw LayoutException::alreadyExists($layout->theme, $layout->type, $layout->elementUid ?? '');
        }

        if ($parent = $layout->parent and !$parent->id) {
            $this->save($parent);
        }

        if (!$layout->hasViewMode(ViewModeService::DEFAULT_HANDLE)) {
            $layout->addViewMode(new ViewMode([
                'name' => \Craft::t('themes', 'Default'),
                'handle' => ViewModeService::DEFAULT_HANDLE
            ]));
        }

        $this->triggerEvent(self::EVENT_BEFORE_SAVE, new LayoutEvent([
            'layout' => $layout,
            'isNew' => $isNew
        ]));

        $projectConfig = \Craft::$app->getProjectConfig();
        $configData = $layout->getConfig();
        $uid = $layout->uid ?? StringHelper::UUID();
        $configPath = self::CONFIG_KEY . '.' . $uid;
        $projectConfig->set($configPath, $configData);

        $record = $this->getRecordByUid($uid);
        $layout->setAttributes($record->getAttributes());

        if ($isNew) {
            $this->add($layout);
        }

        //Saving view modes
        $viewModes = $layout->viewModes;
        foreach ($viewModes as $viewMode) {
            Themes::$plugin->viewModes->save($viewMode);
        }
        Themes::$plugin->viewModes->cleanUp($viewModes, $layout);

        //Saving blocks
        if ($layout->hasBlocks) {
            $blocks = $layout->blocks;
            foreach ($blocks as $block) {
                Themes::$plugin->blocks->save($block);
            }
            Themes::$plugin->blocks->cleanUp($blocks, $layout);
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
        return LayoutRecord::findOne(['uid' => $uid]) ?? new LayoutRecord(['uid' => $uid]);
    }

    /**
     * Handles a change in layout config
     * 
     * @param ConfigEvent $event
     */
    public function handleChanged(ConfigEvent $event)
    {
        $uid = $event->tokenMatches[0];
        $data = $event->newValue;
        if (!$data) {
            //This can happen when fixing broken states
            return;
        }
        $transaction = \Craft::$app->getDb()->beginTransaction();
        try {
            $layout = $this->getRecordByUid($uid);
            $isNew = $layout->getIsNewRecord();

            $layout->type = $data['type'];
            $layout->elementUid = $data['elementUid'];
            $layout->themeHandle = $data['themeHandle'];
            $layout->hasBlocks = $data['hasBlocks'];
            $layout->name = $data['name'];
            $layout->parent_id = null;
            if ($data['parent'] ?? null) {
                $parent = $this->getRecordByUid($data['parent']);
                $layout->parent_id = $parent->id;
            }
            $res = $layout->save(false);
            
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
     * @param ConfigEvent $event
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
     * Callback when an element (entry type, category group etc) is deleted
     * 
     * @param string $uid
     */
    public function onCraftElementDeleted(string $uid)
    {
        $layouts = $this->all->filter(function ($layout) use ($uid) {
            return $layout->elementUid == $uid;
        })->all();
        foreach ($layouts as $layout) {
            $this->delete($layout, true);
        }
    }

    /**
     * Callback when an element (entry type, category group etc) is saved
     * 
     * @param string $type
     * @param string $uid
     */
    public function onCraftElementSaved(string $type, string $uid = '')
    {
        if (!Themes::$plugin->is(Themes::EDITION_PRO)) {
            return;
        }
        foreach ($this->themesRegistry()->getNonPartials() as $theme) {
            $layout = $this->get($theme, $type, $uid);
            if (!$layout) {
                $layout = $this->create([
                    'type' => $type,
                    'elementUid' => $uid,
                    'themeHandle' => $theme->handle,
                ]);
            }
            $this->installLayoutData($layout);
        }
    }

    /**
     * Respond to rebuild config event
     * 
     * @param RebuildConfigEvent $e
     */
    public function rebuildConfig(RebuildConfigEvent $e)
    {
        $parts = explode('.', self::CONFIG_KEY);
        foreach ($this->getAll() as $layout) {
            $e->config[$parts[0]][$parts[1]][$layout->uid] = $layout->getConfig();
        }
    }

    /**
     * Resolve current layout.
     * 
     * @param  string|ThemeInterface $theme theme instance or theme handle
     * @param  Element              $element
     * @return ?LayoutInterface
     */
    public function resolveForRequest($theme, Element $element): ?LayoutInterface
    {
        $theme = $this->getThemeHandle($theme);
        $layout = null;
        if ($element instanceof Category) {
            $layout = $this->get($theme, 'category', $element->getGroup()->uid);
        } elseif ($element instanceof Entry) {
            $layout = $this->get($theme, 'entry', $element->getType()->uid);
        }
        $event = new ResolveRequestLayoutEvent([
            'element' => $element,
            'layout' => $layout,
            'theme' => Themes::$plugin->registry->current
        ]);
        $this->triggerEvent(self::EVENT_RESOLVE_REQUEST_LAYOUT, $event);
        return $event->layout;
    }

    /**
     * Get theme handle from a theme parameter
     * 
     * @param  string|ThemeInterface $theme theme instance or theme handle
     * @throws ThemeException
     * @return string
     */
    protected function getThemeHandle($theme): string
    {
        if ($theme instanceof ThemeInterface) {
            return $theme->handle;
        }
        if (is_string($theme)) {
            return $theme;
        }
        throw ThemeException::wrongParameter(debug_backtrace()[1]['function']);
    }

    /**
     * Create a custom layout
     * 
     * @param  array  $data
     * @return CustomLayout
     */
    public function createCustom(array $data): CustomLayout
    {
        $data['type'] = 'custom';
        $data['hasBlocks'] = true;
        return $this->create($data);
    }

    /**
     * Delete a custom layout
     * 
     * @param  CustomLayout $layout
     * @return bool
     */
    public function deleteCustom(CustomLayout $layout): bool
    {
        return $this->delete($layout);
    }

    /**
     * Deletes a layout
     * 
     * @param  LayoutInterface $layout
     * @param  bool $force
     * @return bool
     * @throws LayoutException
     */
    protected function delete(LayoutInterface $layout, bool $force = false): bool
    {
        if (!$force and ($layout->type == 'default' or $layout->type == 'user')) {
            throw LayoutException::defaultUndeletable();
        }
        $this->triggerEvent(self::EVENT_BEFORE_DELETE, new LayoutEvent([
            'layout' => $layout
        ]));

        //Deleting view modes
        foreach ($layout->viewModes as $viewMode) {
            Themes::$plugin->viewModes->delete($viewMode, true);
        }

        //Deleting blocks
        if ($layout->hasBlocks) {
            foreach ($layout->blocks as $block) {
                Themes::$plugin->blocks->delete($block);
            }
        }

        \Craft::$app->getProjectConfig()->remove(self::CONFIG_KEY . '.' . $layout->uid);

        $this->_layouts = $this->getAll()->where('id', '!=', $layout->id);

        return true;
    }

    /**
     * Add a layout to internal cache
     * 
     * @param LayoutInterface $layout
     */
    protected function add(LayoutInterface $layout)
    {
        if (!$this->getAll()->firstWhere('id', $layout->id)) {
            $this->getAll()->push($layout);
        }
    }

    /**
     * Get all available layouts
     *
     * @param  string $themeHandle
     * @return LayoutInterface[]
     */
    protected function getAvailable(string $themeHandle): array
    {
        $layouts = array_merge(
            [
                $this->create([
                    'type' => 'default',
                    'elementUid' => '',
                    'hasBlocks' => true,
                    'themeHandle' => $themeHandle
                ]),
                $this->create([
                    'type' => 'user',
                    'elementUid' => '',
                    'themeHandle' => $themeHandle
                ])
            ],
            $this->createEntryLayouts($themeHandle),
            $this->createCategoryLayouts($themeHandle),
            $this->createVolumesLayouts($themeHandle),
            $this->createGlobalsLayouts($themeHandle),
            $this->createTagsLayouts($themeHandle),
            $this->getCustomLayouts($themeHandle)
        );
        $event = new AvailableLayoutsEvent([
            'layouts' => $layouts,
            'themeHandle' => $themeHandle
        ]);
        $this->triggerEvent(self::EVENT_AVAILABLE_LAYOUTS, $event);
        return $event->layouts;
    }

    /**
     * Install layout data
     * 
     * @param  LayoutInterface $layout
     * @return bool
     */
    protected function installLayoutData(LayoutInterface $layout): bool
    {
        if ($layout->hasDisplays()) {
            //Creating default view mode
            if (!$layout->hasViewMode(ViewModeService::DEFAULT_HANDLE)) {
                $viewMode = $this->viewModesService()->create([
                    'handle' => ViewModeService::DEFAULT_HANDLE,
                    'name' => \Craft::t('themes', 'Default')
                ]);
                $layout->addViewMode($viewMode);
            }
            foreach ($layout->viewModes as $viewMode) {
                $viewMode->displays = $this->displayService()->createViewModeDisplays($viewMode);
            }
        }
        if (!$this->save($layout)) {
            throw LayoutException::cantSave($layout);
        }
        return true;
    }

    /**
     * Get all custom layouts for a theme
     * 
     * @param  string $themeHandle
     * @return LayoutInterface[]
     */
    protected function getCustomLayouts(string $themeHandle): array
    {
        return $this->getAll()
            ->where('type', 'custom')
            ->where('themeHandle', $themeHandle)
            ->all();
    }

    /**
     * Creates all categories layouts
     *
     * @param  string $themeHandle
     * @return LayoutInterface[]
     */
    protected function createCategoryLayouts(string $themeHandle): array
    {
        $groups = \Craft::$app->categories->getAllGroups();
        $layouts = [];
        foreach ($groups as $group) {
            $layouts[] = $this->create([
                'type' => 'category',
                'elementUid' => $group->uid,
                'themeHandle' => $themeHandle
            ]);
        }
        return $layouts;
    }

    /**
     * Creates all entries layouts
     *
     * @param  string $themeHandle
     * @return LayoutInterface[]
     */
    protected function createEntryLayouts(string $themeHandle): array
    {
        $sections = \Craft::$app->sections->getAllSections();
        $layouts = [];
        foreach ($sections as $section) {
            foreach ($section->getEntryTypes() as $entryType) {
                $layouts[] = $this->create([
                    'type' => 'entry',
                    'elementUid' => $entryType->uid,
                    'themeHandle' => $themeHandle
                ]);
            }
        }
        return $layouts;
    }

    /**
     * Creates all volumes layouts
     *
     * @param  string $themeHandle
     * @return LayoutInterface[]
     */
    protected function createVolumesLayouts(string $themeHandle): array
    {
        $volumes = \Craft::$app->volumes->getAllVolumes();
        $layouts = [];
        foreach ($volumes as $volume) {
            $layouts[] = $this->create([
                'type' => 'volume',
                'elementUid' => $volume->uid,
                'themeHandle' => $themeHandle
            ]);
        }
        return $layouts;
    }

    /**
     * Creates all globals layouts
     *
     * @param  string $themeHandle
     * @return LayoutInterface[]
     */
    protected function createGlobalsLayouts(string $themeHandle): array
    {
        $sets = \Craft::$app->globals->getAllSets();
        $layouts = [];
        foreach ($sets as $set) {
            $layouts[] = $this->create([
                'type' => 'global',
                'elementUid' => $set->uid,
                'themeHandle' => $themeHandle
            ]);
        }
        return $layouts;
    }

    /**
     * Creates all tags layouts
     *
     * @param  string $themeHandle
     * @return LayoutInterface[]
     */
    protected function createTagsLayouts(string $themeHandle): array
    {
        $groups = \Craft::$app->tags->getAllTagGroups();
        $layouts = [];
        foreach ($groups as $group) {
            $layouts[] = $this->create([
                'type' => 'tag',
                'elementUid' => $group->uid,
                'themeHandle' => $themeHandle
            ]);
        }
        return $layouts;
    }
}