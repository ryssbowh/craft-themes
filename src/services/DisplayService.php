<?php 

namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\events\DisplayEvent;
use Ryssbowh\CraftThemes\exceptions\DisplayException;
use Ryssbowh\CraftThemes\models\Display;
use Ryssbowh\CraftThemes\models\DisplayField;
use Ryssbowh\CraftThemes\models\DisplayGroup;
use Ryssbowh\CraftThemes\models\DisplayMatrix;
use Ryssbowh\CraftThemes\models\ViewMode;
use Ryssbowh\CraftThemes\models\layouts\Layout;
use Ryssbowh\CraftThemes\records\DisplayRecord;
use Ryssbowh\CraftThemes\records\FieldRecord;
use Ryssbowh\CraftThemes\records\GroupRecord;
use Ryssbowh\CraftThemes\records\ViewModeRecord;
use craft\base\Field;
use craft\events\ConfigEvent;
use craft\fieldlayoutelements\TitleField;
use craft\helpers\StringHelper;

class DisplayService extends Service
{
    const TYPE_FIELD = 'field';
    const TYPE_MATRIX = 'matrix';
    const TYPE_GROUP = 'group';
    const TYPES = [self::TYPE_FIELD, self::TYPE_GROUP, self::TYPE_MATRIX];

    const EVENT_BEFORE_SAVE = 1;
    const EVENT_AFTER_SAVE = 2;
    const EVENT_BEFORE_APPLY_DELETE = 3;
    const EVENT_AFTER_DELETE = 4;
    const EVENT_BEFORE_DELETE = 5;

    const CONFIG_KEY = 'themes.display';

    public $memoryLoading;

    private $_displays = [];

    public function createAll()
    {
        $viewModesIds = $displayIds = [];
        foreach ($this->layoutService()->getAll(true) as $layout) {
            list($vmIds, $dIds) = $this->createLayoutDisplays($layout);
            $viewModesIds = array_merge($viewModesIds, $vmIds);
            $displayIds = array_merge($displayIds, $dIds);
        }
        $this->viewModeService()->deleteAll($viewModesIds);
        $this->deleteAll($displayIds);
    }

    public function get(ViewMode $viewMode, string $type): ?Display
    {
        if (!in_array($type, self::TYPES)) {
            throw DisplayException::typeInvalid($type, self::TYPES);
        }
        $record = DisplayRecord::find()->with($type)->where([
            'viewMode_id' => $viewMode->id,
            'type' => $type
        ]);
        return $record ? $record->toModel() : null;
    }

    public function getById(int $id): ?Display
    {
        $record = DisplayRecord::find()->where([
            'id' => $id
        ])->with(['viewMode'])->one();
        return $record ? $record->toModel() : null;
    }

    public function getForField(string $fieldUid, ?ViewMode $viewMode = null): array
    {
        $records = DisplayRecord::find()
            ->joinWith(self::TYPE_FIELD)
            ->where(['in' , 'type' , [self::TYPE_FIELD]])
            ->andWhere([FieldRecord::tableName() . '.fieldUid' => $fieldUid]);
        if ($viewMode) {
            $records->andWhere(['viewMode_id' => $viewMode->id]);
        }
        return array_map(function ($record) {
            return $record->toModel();
        }, $records->all());
    }

    public function getForlayout(Layout $layout): array
    {
        $displays = [];
        foreach ($layout->viewModes as $viewMode) {
            $displays = array_merge($displays, $this->getForViewMode($viewMode));
        }
        return $displays;
    }

    /**
     * Get all displays for a view mode
     * 
     * @param  ViewMode $viewMode
     * @return array
     */
    public function getForViewMode(ViewMode $viewMode): array
    {
        $records = DisplayRecord::find()->where([
            'viewMode_id' => $viewMode->id
        ])->with(['viewMode', 'field', 'group', 'matrix'])->orderBy(['order' => SORT_ASC])->all();
        return array_map(function  ($record) {
            return $record->toModel();
        }, $records);
    }

    public function getFieldById(int $id): ?DisplayField
    {
        $record = FieldRecord::find()->with('display')->where(['id' => $id])->one();
        return $record ? $record->toModel() : null;
    }

    public function getGroupById(int $id): ?DisplayGroup
    {
        $record = GroupRecord::find()->where(['id' => $id])->one();
        return $record ? $record->toModel() : null;
    }

    public function getMatrixById(int $id): DisplayMatrix
    {
        $record = MatrixRecord::find()->where(['id' => $id])->one();
        return $record ? $record->toModel() : null;
    }

    /**
     * Get field record by uid or a new one if not found
     * 
     * @param  string $uid
     * @return DisplayRecord
     */
    public function getRecordByUid(string $uid): DisplayRecord
    {
        return DisplayRecord::findOne(['uid' => $uid]) ?? new DisplayRecord;
    }

    public function getFieldRecordByUid(string $uid): ?FieldRecord
    {
        return FieldRecord::find()->where(['uid' => $uid])->one() ?? new FieldRecord;
    }

    public function getMatrixRecordByUid(string $uid): ?MatrixRecord
    {
        return MatrixRecord::find()->where(['uid' => $uid])->one() ?? new MatrixRecord;
    }

    public function getGroupRecordByUid(string $uid): ?GroupRecord
    {
        return GroupRecord::find()->where(['uid' => $uid])->one() ?? new GroupRecord;
    }

    /**
     * Create all displays for a layout.
     * Go through all craft fields defined on the section/category and create display fields for it.
     * 
     * @param  Layout $layout
     */
    protected function createLayoutDisplays(Layout $layout)
    {
        $viewModesIds = $displayIds = [];
        foreach ($layout->viewModes as $viewMode) {
            $viewModeIds[] = $viewMode->id;
            $order = $this->getMaxOrder($viewMode) ?? 0;
            if (!$display = $this->getForField('title', $viewMode)[0] ?? null) {
                $order++;
                $display = $this->createTitleField($viewMode, $order);
            }
            $displayIds[] = $display->id;
            foreach ($layout->element()->getFieldLayout()->getFields() as $craftField) {
                if (!$display = $this->getForField($craftField->uid, $viewMode)[0] ?? null) {
                    $order++;
                    $display = $this->createField($viewMode, $craftField, $order);
                }
                $displayIds[] = $display->id;
            }
        }
        return [$viewModeIds, $displayIds];
    }

    /**
     * Get max order in displays for a view mode
     * 
     * @param  ViewMode $viewMode
     * @return int
     */
    public function getMaxOrder(ViewMode $viewMode): ?int
    {
        $size = sizeof($this->getForViewMode($viewMode));
        if ($size > 0) {
            return $this->getForViewMode($viewMode)[$size - 1]->order;
        }
        return null;
    }

    /**
     * Build a field from raw data
     * 
     * @param  array  $data
     * @return Field
     */
    public function fromData(array $data): Display
    {
        unset($data['availableDisplayers']);
        unset($data['uid']);
        unset($data['name']);
        $itemData = $data['item'];
        unset($data['item']);
        $data['viewMode'] = $this->viewModeService()->getById($data['viewMode_id']);
        if (isset($data['id'])) {
            $display = $this->getById($data['id']);
            $display->setAttributes($data);
            $display->item->setAttributes($itemData);
        } else {
            $display = new DisplayField($data);
            $display->item->setAttributes($itemData);
        }
        return $display;
    }

    /**
     * Delete all displays which id is not in $toKeep for a layout
     * 
     * @param array  $toKeep
     * @param Layout $layout
     */
    public function deleteForLayout(Layout $layout, array $toKeep = [])
    {
        $displays = DisplayRecord::find()
            ->joinWith('viewMode')
            ->where([ViewModeRecord::tableName().'.layout_id' => $layout->id])
            ->andWhere(['not in', DisplayRecord::tableName().'.id', $toKeep])
            ->all();
        foreach ($displays as $display) {
            $this->delete($display->toModel());
        }
    }

    /**
     * Saves one display
     * 
     * @param  Display $display
     * @return bool
     */
    public function save(Display $display, bool $validate = true): bool
    {
        if ($validate and !($display->validate() and $display->item->validate())) {
            return false;
        }
        
        $isNew = !is_int($display->id);
        $uid = $isNew ? StringHelper::UUID() : $display->uid;

        $this->triggerEvent(self::EVENT_BEFORE_SAVE, new DisplayEvent([
            'display' => $display
        ]));

        $projectConfig = \Craft::$app->getProjectConfig();
        $configData = $display->getConfig();
        $configPath = self::CONFIG_KEY . '.' . $uid;
        $projectConfig->set($configPath, $configData);

        $record = $this->getRecordByUid($uid);
        $display->setAttributes($record->getAttributes(), false);
        $display->item->setAttributes($record->item->getAttributes(), false);
        
        return true;
    }

    /**
     * Handles field config change
     * 
     * @param ConfigEvent $event
     */
    public function handleChanged(ConfigEvent $event)
    {
        $uid = $event->tokenMatches[0];
        $data = $event->newValue;
        $transaction = \Craft::$app->getDb()->beginTransaction();
        try {
            $display = $this->getRecordByUid($uid);
            $isNew = $display->getIsNewRecord();

            $display->uid = $uid;
            $display->viewMode_id = $this->viewModeService()->getRecordByUid($data['viewMode_id'])->id;
            $display->type = $data['type'];
            $display->order = $data['order'];
            $display->save(false);

            // example method getFieldRecordByUid for field
            $method = 'get' . ucfirst($data['type']) . 'RecordByUid';
            $item = $this->$method($data['item']['uid']);
            $item->setAttributes($data['item'], false);
            $item->display_id = $display->id;
            $item->save(false);

            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }

        $this->triggerEvent(self::EVENT_AFTER_SAVE, new DisplayEvent([
            'display' => $display,
            'isNew' => $isNew,
        ]));
    }

    public function delete(Display $display): bool
    {
        $this->triggerEvent(self::EVENT_BEFORE_DELETE, new DisplayEvent([
            'display' => $display
        ]));
        \Craft::$app->getProjectConfig()->remove(self::CONFIG_KEY . '.' . $display->uid);

        return true;
    }

    /**
     * Hanles field config deletion
     * 
     * @param ConfigEvent $event
     */
    public function handleDeleted(ConfigEvent $event)
    {
        $uid = $event->tokenMatches[0];
        $record = $this->getRecordByUid($uid);

        if (!$record) {
            return;
        }

        $this->triggerEvent(self::EVENT_BEFORE_APPLY_DELETE, new DisplayEvent([
            'display' => $record
        ]));

        \Craft::$app->getDb()->createCommand()
            ->delete(DisplayRecord::tableName(), ['uid' => $uid])
            ->execute();

        $this->triggerEvent(self::EVENT_AFTER_DELETE, new DisplayEvent([
            'display' => $record
        ]));
    }

    /**
     * Creates a entry type or category layout displays
     * 
     * @param ConfigEvent $event
     */
    public function onCraftElementChanged(ConfigEvent $event)
    {
        $uid = $event->tokenMatches[0];
        foreach ($this->themesRegistry()->getNonPartials() as $theme) {
            $layout = $this->layoutService()->get($theme->handle, $uid);
            $this->createLayoutDisplays($layout);
        }
    }

    /**
     * Delete field records
     * 
     * @param  ConfigEvent $event
     */
    public function onCraftFieldDeleted(ConfigEvent $event)
    {
        $uid = $event->tokenMatches[0];
        foreach ($this->getForField($uid) as $display) {
            $this->delete($display);
        }
    }

    protected function deleteAll(array $toKeep = [])
    {
        $displays = DisplayRecord::find()
            ->with(['viewMode'])
            ->where(['not in', DisplayRecord::tableName() . '.id', $toKeep])
            ->all();
        foreach ($displays as $record) {
            $this->delete($record->toModel());
        }
    }

    protected function createField(ViewMode $viewMode, Field $craftField, int $order = 0): Display
    {
        $class = get_class($craftField);
        $displayer = $this->fieldDisplayersService()->getDefault($class);
        $display = new Display([
            'type' => self::TYPE_FIELD,
            'viewMode_id' => $viewMode->id,
            'order' => $order,
            'hidden' => ($displayer == null),
            'item' => new DisplayField([
                'fieldUid' => $craftField->uid,
                'displayerHandle' => $displayer ? $displayer->handle : '',
                'options' => $displayer ? $displayer->getOptions()->toArray() : []
            ])
        ]);
        $this->save($display);
        return $display;
    }

    protected function createTitleField(ViewMode $viewMode, int $order = 0): Display
    {
        $displayer = $this->fieldDisplayersService()->getDefault(TitleField::class);
        $display = new Display([
            'type' => self::TYPE_FIELD,
            'viewMode_id' => $viewMode->id,
            'order' => $order,
            'hidden' => ($displayer == null),
            'item' => new DisplayField([
                'displayerHandle' => $displayer ? $displayer->handle : '',
                'options' => $displayer ? $displayer->getOptions()->toArray() : [],
                'fieldUid' => 'title'
            ])
        ]);
        $this->save($display);
        return $display;
    }
}