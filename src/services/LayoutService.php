<?php 

namespace Ryssbowh\CraftThemes\services;

use Illuminate\Support\Collection;
use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\events\LayoutEvent;
use Ryssbowh\CraftThemes\exceptions\LayoutException;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use Ryssbowh\CraftThemes\models\PageLayout;
use Ryssbowh\CraftThemes\models\fields\CraftField;
use Ryssbowh\CraftThemes\models\fields\Field;
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
use craft\db\ActiveRecord;
use craft\elements\Category;
use craft\elements\Entry;
use craft\events\ConfigEvent;
use craft\events\EntryTypeEvent;
use craft\events\FieldEvent;
use craft\helpers\StringHelper;

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
     * @var LayoutInterface
     */
    protected $current;

    /**
     * All layouts getter
     * 
     * @return Collection
     */
    public function all(): Collection
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
     * Get all layouts that have displays
     * 
     * @return array
     */
    public function withDisplays(): array
    {
        return $this->all()->filter(function ($layout) {
            return $layout->hasDisplays();
        })->all();
    }

    /**
     * Get all layouts for a theme
     * 
     * @param  string    $theme
     * @param  bool|null $withHasDisplays
     * @param  bool|null $withHasBlocks
     * @return array
     */
    public function getForTheme(string $theme, ?bool $withHasDisplays = null, ?bool $withHasBlocks = null): array
    {
        return $this->all()->filter(function ($layout) use ($theme, $withHasDisplays, $withHasBlocks) {
            if ($layout->theme != $theme) {
                return false;
            }
            if ($withHasDisplays !== null) {
                return $layout->hasDisplays() === $withHasDisplays;
            }
            if ($withHasBlocks !== null) {
                return (bool)$layout->hasBlocks === $withHasBlocks;
            }
            return true;
        })->all();
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
        if ($config instanceof ActiveRecord) {
            $config = $config->getAttributes();
        }
        $viewModesData = $config['viewModes'] ?? null;
        $config['uid'] = $config['uid'] ?? StringHelper::UUID();
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
        foreach (Themes::$plugin->registry->all() as $theme) {
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
        foreach (Themes::$plugin->registry->all() as $theme) {
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
            $ids = array_merge($ids, $this->installThemeData($theme->handle));
        }
        $layouts = $this->all()->whereNotIn('id', $ids)->all();
        foreach ($layouts as $layout) {
            $this->delete($layout, true);
        }
    }

    /**
     * Install layouts for a theme
     * 
     * @param  string $theme
     * @return array
     */
    public function installThemeData(string $theme): array
    {
        $ids = [];
        foreach ($this->getAvailable() as $layout) {
            if (!$layout2 = $this->get($theme, $layout->type, $layout->elementUid)) {
                $layout->theme = $theme;
                $layout2 = $layout;
            }
            $this->installLayoutData($layout2);
            $ids[] = $layout2->id;
        }
        return $ids;
    }

    /**
     * Deletes all layouts for a theme
     * 
     * @param string $theme
     */
    public function uninstallThemeData(string $theme)
    {
        foreach ($this->getForTheme($theme) as $layout) {
            $this->delete($layout, true);
        }
    }

    /**
     * Get default layout for a theme
     * 
     * @param  string  $theme
     * @return ?LayoutInterface
     */
    public function getDefault(string $theme): ?LayoutInterface
    {
        return $this->get($theme, self::DEFAULT_HANDLE);
    }

    /**
     * Get all available layouts
     * 
     * @return array
     */
    public function getAvailable(): array
    {
        return [
            $this->create([
                'type' => self::DEFAULT_HANDLE,
                'elementUid' => '',
                'hasBlocks' => true
            ]),
            $this->create([
                'type' => self::USER_HANDLE,
                'elementUid' => ''
            ]),
            ...$this->getCategoryLayouts(),
            ...$this->getEntryLayouts(),
            ...$this->getVolumesLayouts(),
            ...$this->getGlobalsLayouts(),
            ...$this->getTagsLayouts()
        ];
    }

    /**
     * Get a layout
     * 
     * @param  string  $theme
     * @param  string  $elementUid
     * @param  string  $type
     * @param  boolean $loadBlocks
     * @return ?LayoutInterface
     */
    public function get(string $theme, string $type, string $elementUid = '', bool $loadBlocks = false): ?LayoutInterface
    {
        $layout = $this->all()
            ->where('theme', $theme)
            ->where('elementUid', $elementUid)
            ->firstWhere('type', $type);
        if ($layout and $loadBlocks) {
            return $layout->loadBlocks();
        }
        return $layout;
    }

    /**
     * Save a layout
     * 
     * @param  LayoutInterface $layout
     * @param  bool $validate
     * @return bool
     */
    public function save(LayoutInterface $layout, bool $validate = true): bool
    {
        if ($validate and !$layout->validate()) {
            return false;
        }
        $isNew = !is_int($layout->id);
        $uid = $layout->uid;

        $this->triggerEvent(self::EVENT_BEFORE_SAVE, new LayoutEvent([
            'layout' => $layout
        ]));

        $projectConfig = \Craft::$app->getProjectConfig();
        $configData = $layout->getConfig();
        $configPath = self::CONFIG_KEY . '.' . $uid;
        $projectConfig->set($configPath, $configData);

        $record = $this->getRecordByUid($uid);
        $layout->setAttributes($record->getAttributes(), false);

        foreach ($configData['blocks'] as $index => $data) {
            $record = $this->blocksService()->getRecordByUid($data['uid']);
            $layout->getBlocks()[$index]->uid = $record->uid;
            $layout->getBlocks()[$index]->id = $record->id;
            $layout->getBlocks()[$index]->afterSave();
        }

        foreach ($configData['viewModes'] as $index => $data) {
            $record = $this->viewModesService()->getRecordByUid($data['uid']);
            $layout->getViewModes()[$index]->uid = $record->uid;
            $layout->getViewModes()[$index]->id = $record->id;
        }
        
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
     * @param  LayoutInterface $layout
     * @param  bool $force
     * @return bool
     * @throws LayoutException
     */
    public function delete(LayoutInterface $layout, bool $force = false): bool
    {
        if (!$force and $layout->type == self::DEFAULT_HANDLE) {
            throw LayoutException::defaultUndeletable();
        }
        $this->triggerEvent(self::EVENT_BEFORE_DELETE, new LayoutEvent([
            'layout' => $layout
        ]));

        \Craft::$app->getProjectConfig()->remove(self::CONFIG_KEY . '.' . $layout->uid);

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
            $layout->elementUid = $data['elementUid'];
            $layout->theme = $data['theme'];
            $layout->hasBlocks = $data['hasBlocks'];
            $layout->save(false);

            $this->viewModesService()->saveMany($data['viewModes'] ?? [], $layout);
            $this->blocksService()->saveMany($data['blocks'] ?? [], $layout);
            $this->displayService()->saveMany($data['displays'] ?? [], $layout);
            
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
        });
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
    public function onCraftElementSaved(string $type, string $uid)
    {
        foreach ($this->themesRegistry()->getNonPartials() as $theme) {
            $layout = $this->get($theme->handle, $type, $uid);
            if (!$layout) {
                $layout = $this->create([
                    'type' => $type,
                    'elementUid' => $uid,
                    'theme' => $theme->handle,
                ]);
            }
            $this->installLayoutData($layout);
        }
    }

    /**
     * Callback when a field is deleted
     * Resave layout for which a deleted field was present
     * 
     * @param  ConfigEvent $event
     */
    public function onCraftFieldDeleted(FieldEvent $event)
    {
        $layoutsSaved = [];
        foreach ($this->displayService()->getAllForCraftField($event->field->id) as $display) {
            $layout = $display->viewMode->layout;
            if (in_array($layout->id, $layoutsSaved)) {
                continue;
            }
            $this->installLayoutData($layout);
            $layoutsSaved[] = $layout->id;
        }
    }

    /**
     * handles a craft field save: Replaces the display in each layout 
     * where the craft field was referenced (if the type of field has changed) and saves the layout.
     * 
     * @param  FieldEvent $event
     */
    public function onCraftFieldSaved(FieldEvent $event)
    {
        if ($event->isNew) {
            return;
        }
        $field = $event->field;
        $displays = $this->displayService()->getAllForCraftField($field->id);
        $toSave = [];
        foreach ($displays as $display) {
            $oldItem = $display->item;
            $oldFieldClass = $oldItem->craft_field_class;
            if ($oldItem->craft_field_class != get_class($field)) {
                $this->fieldsService()->deleteField($oldItem);
                $display->item = CraftField::createFromField($field);
                $display->item->labelHidden = $oldItem->labelHidden;
                $display->item->labelVisuallyHidden = $oldItem->labelVisuallyHidden;
                $display->item->visuallyHidden = $oldItem->visuallyHidden;
                $display->item->hidden = $display->item->hidden ?: $oldItem->hidden;
                $display->item->display = $display;
                $layout = $display->viewMode->layout;
                if (!isset($toSave[$layout->id])) {
                    $toSave[$layout->id] = $layout;
                } else {
                    $layout = $toSave[$layout->id];
                }
                $layout->replaceDisplay($display);
            }
        }
        foreach ($toSave as $layout) {
            $this->save($layout);
        }
    }

    /**
     * Resolve current layout.
     * 
     * @param  string $theme
     * @param  mixed  $element
     * @return ?LayoutInterface
     */
    public function resolveForRequest(string $theme, $element): ?LayoutInterface
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

    /**
     * get current layout
     * 
     * @return ?LayoutInterface
     */
    public function getCurrent(): ?LayoutInterface
    {
        return $this->current;
    }

    /**
     * Install layout data
     * 
     * @param LayoutInterface $layout
     */
    protected function installLayoutData(LayoutInterface $layout)
    {
        if (!$layout->viewModes) {
            $viewMode = $this->viewModesService()->create([
                'handle' => ViewModeService::DEFAULT_HANDLE,
                'name' => \Craft::t('themes', 'Default'),
                'layout' => $layout
            ]);
            $layout->viewModes = [$viewMode];
        }
        $layout->displays = $this->displayService()->createLayoutDisplays($layout);
        $this->save($layout);
    }

    /**
     * Creates all categories layouts
     *
     * @return array
     */
    protected function getCategoryLayouts(): array
    {
        $groups = \Craft::$app->categories->getAllGroups();
        $layouts = [];
        foreach ($groups as $group) {
            $layouts[] = $this->create([
                'type' => self::CATEGORY_HANDLE,
                'elementUid' => $group->uid
            ]);
        }
        return $layouts;
    }

    /**
     * Creates all entries layouts
     *
     * @return array
     */
    protected function getEntryLayouts(): array
    {
        $sections = \Craft::$app->sections->getAllSections();
        $layouts = [];
        foreach ($sections as $section) {
            foreach ($section->getEntryTypes() as $entryType) {
                $layouts[] = $this->create([
                    'type' => self::ENTRY_HANDLE,
                    'elementUid' => $entryType->uid
                ]);
            }
        }
        return $layouts;
    }

    /**
     * Creates all volumes layouts
     *
     * @return array
     */
    protected function getVolumesLayouts(): array
    {
        $volumes = \Craft::$app->volumes->getAllVolumes();
        $layouts = [];
        foreach ($volumes as $volume) {
            $layouts[] = $this->create([
                'type' => self::VOLUME_HANDLE,
                'elementUid' => $volume->uid
            ]);
        }
        return $layouts;
    }

    /**
     * Creates all globals layouts
     *
     * @return array
     */
    protected function getGlobalsLayouts(): array
    {
        $sets = \Craft::$app->globals->getAllSets();
        $layouts = [];
        foreach ($sets as $set) {
            $layouts[] = $this->create([
                'type' => self::GLOBAL_HANDLE,
                'elementUid' => $set->uid
            ]);
        }
        return $layouts;
    }

    /**
     * Creates all tags layouts
     *
     * @return array
     */
    protected function getTagsLayouts(): array
    {
        $groups = \Craft::$app->tags->getAllTagGroups();
        $layouts = [];
        foreach ($groups as $group) {
            $layouts[] = $this->create([
                'type' => self::TAG_HANDLE,
                'elementUid' => $group->uid
            ]);
        }
        return $layouts;
    }
}