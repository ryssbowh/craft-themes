<?php

namespace Ryssbowh\CraftThemes\services;

use Illuminate\Support\Collection;
use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\events\LayoutEvent;
use Ryssbowh\CraftThemes\exceptions\LayoutException;
use Ryssbowh\CraftThemes\exceptions\ThemeException;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use Ryssbowh\CraftThemes\models\PageLayout;
use Ryssbowh\CraftThemes\models\fields\CraftField;
use Ryssbowh\CraftThemes\models\layouts\CategoryLayout;
use Ryssbowh\CraftThemes\models\layouts\EntryLayout;
use Ryssbowh\CraftThemes\models\layouts\GlobalLayout;
use Ryssbowh\CraftThemes\models\layouts\Layout;
use Ryssbowh\CraftThemes\models\layouts\TagLayout;
use Ryssbowh\CraftThemes\models\layouts\UserLayout;
use Ryssbowh\CraftThemes\models\layouts\VolumeLayout;
use Ryssbowh\CraftThemes\records\BlockRecord;
use Ryssbowh\CraftThemes\records\LayoutRecord;
use Ryssbowh\CraftThemes\records\ViewModeRecord;
use Ryssbowh\CraftThemes\services\DisplayService;
use craft\elements\Category;
use craft\elements\Entry;
use craft\events\ConfigEvent;
use craft\events\EntryTypeEvent;
use craft\events\RebuildConfigEvent;

class LayoutService extends Service
{
    const EVENT_BEFORE_SAVE = 'before_save';
    const EVENT_AFTER_SAVE = 'after_save';
    const EVENT_BEFORE_APPLY_DELETE = 'before_apply_delete';
    const EVENT_AFTER_DELETE = 'after_delete';
    const EVENT_BEFORE_DELETE = 'before_delete';
    const CONFIG_KEY = 'themes.layouts';

    const DEFAULT_HANDLE = 'default';
    const CATEGORY_HANDLE = 'category';
    const ENTRY_HANDLE = 'entry';
    const USER_HANDLE = 'user';
    const VOLUME_HANDLE = 'volume';
    const GLOBAL_HANDLE = 'global';
    const TAG_HANDLE = 'tag';

    const TYPES = [self::DEFAULT_HANDLE, self::CATEGORY_HANDLE, self::ENTRY_HANDLE, self::USER_HANDLE, self::VOLUME_HANDLE, self::GLOBAL_HANDLE, self::TAG_HANDLE];

    /**
     * @var Collection
     */
    protected $_layouts;

    /**
     * All layouts getter
     * 
     * @return Collection
     */
    public function all(): Collection
    {
        if ($this->_layouts === null) {
            $records = LayoutRecord::find()->all();
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
     * @return LayoutInterface
     * @throws LayoutException
     */
    public function getById(int $id): LayoutInterface
    {
        if ($layout = $this->all()->firstWhere('id', $id)) {
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
        if ($layout = $this->all()->firstWhere('uid', $uid)) {
            return $layout;
        }
        throw LayoutException::noUid($uid);
    }

    /**
     * Get all layouts that have displays
     * 
     * @return array
     */
    public function withDisplays(): array
    {
        return $this->all()->filter(function ($layout) {
            return $layout->hasDisplays();
        })->values()->all();
    }

    /**
     * Get all layouts for a theme
     * 
     * @param  string|ThemeInterface $theme
     * @param  bool|null $withHasDisplays
     * @param  bool|null $withHasBlocks
     * @return array
     */
    public function getForTheme($theme, ?bool $withHasDisplays = null, ?bool $withHasBlocks = null): array
    {
        $theme = $this->getThemeHandle($theme);
        return $this->all()->filter(function ($layout) use ($theme, $withHasDisplays, $withHasBlocks) {
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
     * Get all layouts with blocks indexed by theme's handle
     * 
     * @return array
     */
    public function getBlockLayouts(): array
    {
        $layouts = [];
        foreach (Themes::$plugin->registry->all() as $theme) {
            $layouts[$theme->handle] = $this->all()->filter(function ($layout) use ($theme) {
                return ($layout->canHaveUrls() and $layout->themeHandle == $theme->handle);
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
    public function getWithDisplays(): array
    {
        $layouts = [];
        foreach (Themes::$plugin->registry->all() as $theme) {
            $layouts[$theme->handle] = $this->all()->filter(function ($layout) use ($theme) {
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
     * Create all layouts for all themes
     */
    public function install()
    {
        foreach ($this->themesRegistry()->getNonPartials() as $theme) {
            $this->installThemeData($theme);
        }
    }

    /**
     * Install layouts for a theme, will deletes orphans
     * 
     * @param  ThemeInterface $theme
     * @throws ThemeException
     * @return bool
     */
    public function installThemeData(ThemeInterface $theme): bool
    {
        if ($theme->isPartial()) {
            return false;
        }
        $ids = [];
        foreach ($this->getAvailable($theme->handle) as $layout) {
            if (!$layout2 = $this->get($theme, $layout->type, $layout->elementUid)) {
                $layout->themeHandle = $theme->handle;
                $layout2 = $layout;
            }
            $this->installLayoutData($layout2);
            $ids[] = $layout2->id;
        }
        $layouts = $this->all()
            ->whereNotIn('id', $ids)
            ->where(['themeHandle' => $theme->handle])
            ->all();
        foreach ($layouts as $layout) {
            $this->delete($layout, true);
        }
        return true;
    }

    /**
     * Deletes all layouts for a theme
     * 
     * @param  ThemeInterface $theme
     * @throws ThemeException
     */
    public function uninstallThemeData(ThemeInterface $theme)
    {
        foreach ($this->getForTheme($theme) as $layout) {
            $this->delete($layout, true);
        }
    }

    /**
     * Get default layout for a theme
     * 
     * @param  string|ThemeInterface  $theme
     * @return ?LayoutInterface
     */
    public function getDefault($theme): ?LayoutInterface
    {
        return $this->get($theme, self::DEFAULT_HANDLE);
    }

    /**
     * Get a layout
     * 
     * @param  string|ThemeInterface $theme
     * @param  string                $elementUid
     * @param  string                $type
     * @param  boolean               $loadBlocks
     * @return ?LayoutInterface
     */
    public function get($theme, string $type, string $elementUid = ''): ?LayoutInterface
    {
        $theme = $this->getThemeHandle($theme);
        $layout = $this->all()
            ->where('themeHandle', $theme)
            ->where('elementUid', $elementUid)
            ->firstWhere('type', $type);
        return $layout;
    }

    /**
     * Returns all layouts for a type
     * 
     * @param  string|ThemeInterface $theme
     * @param  string                $type
     * @return array
     */
    public function getForType($theme, string $type): array
    {
        $theme = $this->getThemeHandle($theme);
        return $this->all()
            ->where('themeHandle', $theme)
            ->where('type', $type)
            ->values()
            ->all();
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

        $this->triggerEvent(self::EVENT_BEFORE_SAVE, new LayoutEvent([
            'layout' => $layout,
            'isNew' => $isNew
        ]));

        $projectConfig = \Craft::$app->getProjectConfig();
        $configData = $layout->getConfig();
        $uid = $configData['uid'];
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
        $blocks = $layout->blocks;
        foreach ($blocks as $block) {
            Themes::$plugin->blocks->save($block);
        }
        Themes::$plugin->blocks->cleanUp($blocks, $layout);

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
        $transaction = \Craft::$app->getDb()->beginTransaction();
        try {
            $layout = $this->getRecordByUid($uid);
            $isNew = $layout->getIsNewRecord();

            $layout->type = $data['type'];
            $layout->elementUid = $data['elementUid'];
            $layout->themeHandle = $data['themeHandle'];
            $layout->hasBlocks = $data['hasBlocks'];
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
     * Callback when an element (entry type, category group etc) is deleted
     * 
     * @param string $type
     * @param string $uid
     */
    public function onCraftElementDeleted(string $type, string $uid)
    {
        $layouts = $this->all()->filter(function ($layout) use ($uid) {
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
    public function onCraftElementSaved(string $type, string $uid, $type2 = null)
    {
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
        foreach ($this->all() as $layout) {
            $e->config[self::CONFIG_KEY.'.'.$layout->uid] = $layout->getConfig();
        }
    }

    /**
     * Resolve current layout.
     * 
     * @param  string|ThemeInterface $theme
     * @param  mixed                 $element
     * @return ?LayoutInterface
     */
    public function resolveForRequest($theme, $element): ?LayoutInterface
    {
        $theme = $this->getThemeHandle($theme);
        $layout = null;
        if ($element instanceof Category) {
            $layout = $this->get($theme, self::CATEGORY_HANDLE, $element->getGroup()->uid);
        } elseif ($element instanceof Entry) {
            $layout = $this->get($theme, self::ENTRY_HANDLE, $element->getType()->uid);
        }
        return $layout;
    }

    /**
     * Get theme handle from a theme parameter
     * 
     * @param  string|ThemeInterface $theme
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
     * Create a layout from config
     * 
     * @param  array|ActiveRecord $config
     * @return LayoutInterface
     * @throws LayoutException
     */
    protected function create($config): LayoutInterface
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
        switch ($config['type']) {
            case self::DEFAULT_HANDLE:
                $layout = new Layout;
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

        $config = array_intersect_key($config, array_flip($layout->safeAttributes()));
        $layout->setAttributes($config);
        return $layout;
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
        if (!$force and ($layout->type == self::DEFAULT_HANDLE or $layout->type == self::USER_HANDLE)) {
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
        foreach ($layout->blocks as $block) {
            Themes::$plugin->blocks->delete($block);
        }

        \Craft::$app->getProjectConfig()->remove(self::CONFIG_KEY . '.' . $layout->uid);

        $this->_layouts = $this->all()->where('id', '!=', $layout->id);

        return true;
    }

    /**
     * Add a layout to internal cache
     * 
     * @param LayoutInterface $layout
     */
    protected function add(LayoutInterface $layout)
    {
        if (!$this->all()->firstWhere('id', $layout->id)) {
            $this->all()->push($layout);
        }
    }

    /**
     * Get all available layouts
     *
     * @param  string $themeHandle
     * @return array
     */
    protected function getAvailable(string $themeHandle): array
    {
        return [
            $this->create([
                'type' => self::DEFAULT_HANDLE,
                'elementUid' => '',
                'hasBlocks' => true,
                'themeHandle' => $themeHandle
            ]),
            $this->create([
                'type' => self::USER_HANDLE,
                'elementUid' => '',
                'themeHandle' => $themeHandle
            ]),
            ...$this->createCategoryLayouts($themeHandle),
            ...$this->createEntryLayouts($themeHandle),
            ...$this->createVolumesLayouts($themeHandle),
            ...$this->createGlobalsLayouts($themeHandle),
            ...$this->createTagsLayouts($themeHandle)
        ];
    }

    /**
     * Install layout data
     * 
     * @param  LayoutInterface $layout
     * @return bool
     */
    protected function installLayoutData(LayoutInterface $layout): bool
    {
        //Creating default view mode, except for default layout :
        if ($layout->type == self::DEFAULT_HANDLE) {
            $layout->viewModes = [];
        } else if (!$layout->viewModes) {
            $viewMode = $this->viewModesService()->create([
                'handle' => ViewModeService::DEFAULT_HANDLE,
                'name' => \Craft::t('themes', 'Default'),
                'layout' => $layout
            ]);
            $layout->viewModes = [$viewMode];
        }
        foreach ($layout->viewModes as $viewMode) {
            $viewMode->displays = $this->displayService()->createViewModeDisplays($viewMode);
        }
        return $this->save($layout);
    }

    /**
     * Creates all categories layouts
     *
     * @param  string $themeHandle
     * @return array
     */
    protected function createCategoryLayouts(string $themeHandle): array
    {
        $groups = \Craft::$app->categories->getAllGroups();
        $layouts = [];
        foreach ($groups as $group) {
            $layouts[] = $this->create([
                'type' => self::CATEGORY_HANDLE,
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
     * @return array
     */
    protected function createEntryLayouts(string $themeHandle): array
    {
        $sections = \Craft::$app->sections->getAllSections();
        $layouts = [];
        foreach ($sections as $section) {
            foreach ($section->getEntryTypes() as $entryType) {
                $layouts[] = $this->create([
                    'type' => self::ENTRY_HANDLE,
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
     * @return array
     */
    protected function createVolumesLayouts(string $themeHandle): array
    {
        $volumes = \Craft::$app->volumes->getAllVolumes();
        $layouts = [];
        foreach ($volumes as $volume) {
            $layouts[] = $this->create([
                'type' => self::VOLUME_HANDLE,
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
     * @return array
     */
    protected function createGlobalsLayouts(string $themeHandle): array
    {
        $sets = \Craft::$app->globals->getAllSets();
        $layouts = [];
        foreach ($sets as $set) {
            $layouts[] = $this->create([
                'type' => self::GLOBAL_HANDLE,
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
     * @return array
     */
    protected function createTagsLayouts(string $themeHandle): array
    {
        $groups = \Craft::$app->tags->getAllTagGroups();
        $layouts = [];
        foreach ($groups as $group) {
            $layouts[] = $this->create([
                'type' => self::TAG_HANDLE,
                'elementUid' => $group->uid,
                'themeHandle' => $themeHandle
            ]);
        }
        return $layouts;
    }
}