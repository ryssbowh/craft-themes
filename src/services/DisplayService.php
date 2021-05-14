<?php 

namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use Ryssbowh\CraftThemes\models\Display;
use Ryssbowh\CraftThemes\models\ViewMode;
use Ryssbowh\CraftThemes\models\fields\Title;
use Ryssbowh\CraftThemes\models\layouts\Layout;
use Ryssbowh\CraftThemes\records\DisplayRecord;
use Ryssbowh\CraftThemes\records\FieldRecord;
use Ryssbowh\CraftThemes\records\GroupRecord;
use Ryssbowh\CraftThemes\records\LayoutRecord;
use Ryssbowh\CraftThemes\records\MatrixPivotRecord;
use Ryssbowh\CraftThemes\records\ViewModeRecord;
use craft\base\Field;
use craft\db\ActiveRecord;
use craft\fieldlayoutelements\TitleField;
use craft\fields\Matrix;
use craft\helpers\StringHelper;
use craft\models\MatrixBlockType;

class DisplayService extends Service
{
    const TYPE_FIELD = 'field';
    const TYPE_GROUP = 'group';
    const TYPES = [self::TYPE_FIELD, self::TYPE_GROUP];

    const EVENT_BEFORE_SAVE = 1;
    const EVENT_AFTER_SAVE = 2;
    const EVENT_BEFORE_APPLY_DELETE = 3;
    const EVENT_AFTER_DELETE = 4;
    const EVENT_BEFORE_DELETE = 5;

    private $_displays;

    public function all()
    {
        if ($this->_displays === null) {
            $records = DisplayRecord::find()->all();
            $this->_displays = collect();
            foreach ($records as $record) {
                $this->_displays->push($this->create($record));
            }
        }
        return $this->_displays;
    }

    public function create($config): Display
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
            $display->item = $this->fieldsService()->create($itemData);
        }
        return $display;
    }

    public function getById(int $id): ?Display
    {
        return $this->all()->firstWhere('id', $id);
    }

    public function getForCraftField(?int $fieldId = null, ViewMode $viewMode): ?Display
    {
        return $this->all()
            ->where('type', self::TYPE_FIELD)
            ->where('item.craft_field_id', $fieldId)
            ->firstWhere('viewMode_id', $viewMode->id);
    }

    public function getAllForCraftField(int $fieldId): array
    {
        return $this->all()
            ->where('type', self::TYPE_FIELD)
            ->where('item.craft_field_id', $fieldId)
            ->values()
            ->all();
    }

    public function getForTitleField(ViewMode $viewMode = null): ?Display
    {
        return $this->all()
            ->where('type', self::TYPE_FIELD)
            ->where('viewMode_id', $viewMode->id)
            ->firstWhere('item.type', FieldsService::TYPE_TITLE);
    }

    public function getForLayout(Layout $layout): array
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

    public function saveMany(array $data, LayoutRecord $layout)
    {
        $ids = [];
        foreach ($data as $displayData) {
            $viewModeId = $this->viewModesService()->getRecordByUid($displayData['viewMode_id'])->id;
            $display = $this->getRecordByUid($displayData['uid']);
            $display->viewMode_id = $viewModeId;
            $display->type = $displayData['type'];
            $display->order = $displayData['order'];
            $display->save(false);
            $ids[$viewModeId][] = $display->id;
            $this->fieldsService()->save($displayData['item'], $display);
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
     * Go through all craft fields defined on the section/category and create display and fields for it.
     * 
     * @param  Layout $layout
     */
    public function createLayoutDisplays(Layout $layout): array
    {
        $displays = [];
        foreach ($layout->viewModes as $viewMode) {
            $order = $this->getMaxOrder($viewMode) ?? 0;
            try {
                $display = $this->getForTitleField($viewMode);
            } catch (\Throwable $e) {
                $display = null;
            }
            if (!$display) {
                $order++;
                $display = $this->create([
                    'type' => self::TYPE_FIELD,
                    'viewMode' => $viewMode,
                    'order' => $order,
                ]);
                $display->item = $this->fieldsService()->createTitleField();
            }
            $displays[] = $display;
            foreach ($layout->getCraftFields() as $craftField) {
                try {
                    $display = $this->getForCraftField($craftField->id, $viewMode);
                } catch (\Throwable $e) {
                    $display = null;
                }
                if (!$display) {
                    $order++;
                    $display = $this->create([
                        'type' => self::TYPE_FIELD,
                        'viewMode' => $viewMode,
                        'order' => $order,
                    ]);
                    $display->item = $this->fieldsService()->createField($craftField);
                }
                $displays[] = $display;
            }
        }
        return $displays;
    }

    /**
     * Get max order in displays for a view mode
     * 
     * @param  ViewMode $viewMode
     * @return int
     */
    protected function getMaxOrder(ViewMode $viewMode): ?int
    {
        $size = sizeof($this->getForViewMode($viewMode));
        if ($size > 0) {
            return $this->getForViewMode($viewMode)[$size - 1]->order;
        }
        return null;
    }

    /**
     * Get field record by uid or a new one if not found
     * 
     * @param  string $uid
     * @return DisplayRecord
     */
    protected function getRecordByUid(string $uid): DisplayRecord
    {
        return DisplayRecord::findOne(['uid' => $uid]) ?? new DisplayRecord;
    }
}