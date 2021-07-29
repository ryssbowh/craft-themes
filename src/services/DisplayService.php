<?php 

namespace Ryssbowh\CraftThemes\services;

use Illuminate\Support\Collection;
use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\DisplayInterface;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\Display;
use Ryssbowh\CraftThemes\models\ViewMode;
use Ryssbowh\CraftThemes\models\fields\CraftField;
use Ryssbowh\CraftThemes\models\fields\Matrix;
use Ryssbowh\CraftThemes\records\DisplayRecord;
use Ryssbowh\CraftThemes\records\LayoutRecord;
use craft\db\ActiveRecord;
use craft\fields\Matrix as CraftMatrix;
use craft\helpers\StringHelper;

class DisplayService extends Service
{
    const TYPE_FIELD = 'field';
    const TYPE_GROUP = 'group';
    const TYPES = [self::TYPE_FIELD, self::TYPE_GROUP];

    const EVENT_BEFORE_SAVE = 'before_save';
    const EVENT_AFTER_SAVE = 'after_save';
    const EVENT_BEFORE_APPLY_DELETE = 'before_apply_delete';
    const EVENT_AFTER_DELETE = 'after_delete';
    const EVENT_BEFORE_DELETE = 'before_delete';

    /**
     * @var Collection
     */
    private $_displays;

    /**
     * Get all displays
     * 
     * @return Collection
     */
    public function all(): Collection
    {
        if ($this->_displays === null) {
            $records = DisplayRecord::find()->orderBy('order asc')->all();
            $this->_displays = collect();
            foreach ($records as $record) {
                $this->_displays->push($this->create($record));
            }
        }
        return $this->_displays;
    }

    /**
     * Create a display from config
     * 
     * @param  array|ActiveRecord $config
     * @return DisplayInterface
     */
    public function create($config): DisplayInterface
    {
        if ($config instanceof ActiveRecord) {
            $config = $config->getAttributes();
        }
        $config['uid'] = $config['uid'] ?? StringHelper::UUID();
        $itemData = $config['item'] ?? null;
        $display = new Display;
        $attributes = $display->safeAttributes();
        $config = array_intersect_key($config, array_flip($attributes));
        $display->setAttributes($config);
        if ($itemData) {
            if ($display->type == self::TYPE_FIELD) {
                $display->item = $this->fieldsService()->create($itemData);
            } else {
                $display->item = $this->groupsService()->create($itemData);
            }
        }
        return $display;
    }

    /**
     * Get a display by id
     * 
     * @param  int    $id
     * @return ?DisplayInterface
     */
    public function getById(int $id): ?DisplayInterface
    {
        return $this->all()->firstWhere('id', $id);
    }

    /**
     * Get a display by uid
     * 
     * @param  int    $uid
     * @return ?DisplayInterface
     */
    public function getByUid(string $uid): ?DisplayInterface
    {
        return $this->all()->firstWhere('uid', $uid);
    }

    /**
     * Get all displays for a craft field id
     * 
     * @param  int    $fieldId
     * @return array
     */
    public function getAllForCraftField(int $fieldId): array
    {
        return $this->all()
            ->where('type', self::TYPE_FIELD)
            ->where('item.craft_field_id', $fieldId)
            ->values()
            ->all();
    }

    /**
     * Get all displays for a view mode and a field type
     * 
     * @param  ViewMode $viewMode
     * @param  string   $type
     * @return ?DisplayInterface
     */
    public function getForFieldType(ViewMode $viewMode, string $type): ?DisplayInterface
    {
        return $this->all()
            ->where('type', self::TYPE_FIELD)
            ->where('viewMode_id', $viewMode->id)
            ->firstWhere('item.type', $type);
    }

    /**
     * Get all displays for a layout
     * 
     * @param  LayoutInterface $layout
     * @return array
     */
    public function getForLayout(LayoutInterface $layout): array
    {
        if (!$layout->id) {
            return [];
        }
        $viewModes = array_map(function ($viewMode) {
            return $viewMode->id;
        }, $layout->viewModes);
        return $this->all()
            ->whereIn('viewMode_id', $viewModes)
            ->values()
            ->all();
    }

    /**
     * Get all displays for a group
     * 
     * @param  int $groupId
     * @return array
     */
    public function getForGroup(int $groupId): array
    {
        return $this->all()
            ->where('group_id', $groupId)
            ->values()
            ->all();
    }

    /**
     * Get all displays for a view mode
     * 
     * @param  ViewMode $viewMode
     * @return array
     */
    public function getForViewMode(ViewMode $viewMode): array
    {
        return $this->all()
            ->where('viewMode_id', $viewMode->id)
            ->values()
            ->all();
    }

    /**
     * Saves one display
     * 
     * @param  array  $data
     * @return DisplayRecord
     */
    public function save(array $data): DisplayRecord
    {
        $display = $this->getRecordByUid($data['uid']);
        if ($data['viewMode_id']) {
            $viewModeId = $this->viewModesService()->getRecordByUid($data['viewMode_id'])->id;
            $display->viewMode_id = $viewModeId;
        } else {
            $display->viewMode_id = null;
        }
        if ($data['group_id']) {
            $groupId = $this->displayService()->getRecordByUid($data['group_id'])->id;
            $display->group_id = $groupId;
        } else {
            $display->group_id = null;
        }
        $display->type = $data['type'];
        $display->order = $data['order'];
        $display->save(false);
        if ($data['type'] == self::TYPE_FIELD) {
            $this->fieldsService()->save($data['item'], $display);
        } else {
            $this->groupsService()->save($data['item'], $display);
        }
        return $display;
    }

    /**
     * Saves display data for a layout
     * 
     * @param array        $data
     * @param LayoutRecord $layout
     */
    public function saveMany(array $data, LayoutRecord $layout)
    {
        $ids = [];
        $saveLater = [];
        foreach ($data as $displayData) {
            if ($displayData['group_id']) {
                //Saving this display later to make sure its group has been saved
                $saveLater[] = $displayData;
                continue;
            }
            $display = $this->save($displayData);
            $ids[$display->viewMode->id][] = $display->id;
        }
        foreach ($saveLater as $displayData) {
            $display = $this->save($displayData);
            $ids[$display->viewMode->id][] = $display->id;
        }

        foreach ($ids as $viewModeId => $displayIds) {
            $toDelete = DisplayRecord::find()
                ->where(['viewMode_id' => $viewModeId])
                ->andWhere(['not in', 'id', $displayIds])
                ->all();
            foreach ($toDelete as $display) {
                $display->delete();
            }
        }
        $this->_displays = null;
    }

    /**
     * Create all displays for a layout.
     * Go through all craft fields defined on the layout and create display and fields for it.
     * 
     * @param LayoutInterface $layout
     */
    public function createLayoutDisplays(LayoutInterface $layout): array
    {
        $displays = [];
        foreach ($layout->viewModes as $viewMode) {
            //Keeping all the groups defined in this view mode 
            $groups = array_values(array_filter($this->getForViewMode($viewMode), function ($display) {
                return $display->type == self::TYPE_GROUP;
            }));
            $displays = array_merge($displays, $groups);
            $order = $this->getNextOrder($viewMode);
            //Getting or creating displays for fields that are not craft fields (author, title etc)
            foreach (Themes::$plugin->fields->registeredFields as $fieldType => $fieldClass) {
                if ($fieldClass::shouldExistOnLayout($layout)) {
                    try {
                        $display = $this->getForFieldType($viewMode, $fieldType);
                    } catch (\Throwable $e) {
                        $display = null;
                    }
                    if (!$display) {
                        $display = $this->create([
                            'type' => self::TYPE_FIELD,
                            'viewMode' => $viewMode,
                            'order' => $order,
                        ]);
                        $order++;
                        $display->item = $fieldClass::create();
                    }
                    $displays[] = $display;
                }
            }
            //Getting or creating displays for craft fields
            foreach ($layout->getCraftFields() as $craftField) {
                try {
                    $display = $this->getForCraftField($craftField->id, $viewMode);
                } catch (\Throwable $e) {
                    $display = null;
                }
                if (!$display) {
                    $display = $this->create([
                        'type' => self::TYPE_FIELD,
                        'viewMode' => $viewMode,
                        'order' => $order,
                    ]);
                    $order++;
                    $display->item = Themes::$plugin->fields->createFromField($craftField);
                }
                $displays[] = $display;
            }
        }
        return $displays;
    }

    /**
     * Get a display for a craft field and a view mode
     * 
     * @param  int|null $fieldId
     * @param  ViewMode $viewMode
     * @return ?DisplayInterface
     */
    protected function getForCraftField(?int $fieldId = null, ViewMode $viewMode): ?DisplayInterface
    {
        return $this->all()
            ->where('type', self::TYPE_FIELD)
            ->where('item.craft_field_id', $fieldId)
            ->firstWhere('viewMode_id', $viewMode->id);
    }

    /**
     * Get next order in displays for a view mode
     * 
     * @param  ViewMode $viewMode
     * @return int
     */
    protected function getNextOrder(ViewMode $viewMode): ?int
    {
        $displays = $this->getForViewMode($viewMode);
        if (sizeof($displays) > 0) {
            return $displays[sizeof($displays) - 1]->order + 1;
        }
        return 0;
    }

    /**
     * Get field record by uid or a new one if not found
     * 
     * @param  string $uid
     * @return DisplayRecord
     */
    public function getRecordByUid(string $uid): DisplayRecord
    {
        return DisplayRecord::findOne(['uid' => $uid]) ?? new DisplayRecord(['uid' => $uid]);
    }
}