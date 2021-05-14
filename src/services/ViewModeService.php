<?php 

namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\events\ViewModeEvent;
use Ryssbowh\CraftThemes\exceptions\ViewModeException;
use Ryssbowh\CraftThemes\models\ViewMode;
use Ryssbowh\CraftThemes\models\layouts\Layout;
use Ryssbowh\CraftThemes\records\LayoutRecord;
use Ryssbowh\CraftThemes\records\ViewModeRecord;
use craft\events\ConfigEvent;
use craft\helpers\StringHelper;

class ViewModeService extends Service
{
    const DEFAULT_HANDLE = 'default';

    protected $_viewModes;

    public function all()
    {
        if ($this->_viewModes === null) {
            $records = ViewModeRecord::find()->all();
            $this->_viewModes = collect();
            foreach ($records as $record) {
                $this->_viewModes->push($this->create($record));
            }
        }
        return $this->_viewModes;
    }

    public function create($config): ViewMode
    {
        if ($config instanceof ActiveRecord) {
            $config = $config->getAttributes();
        }
        return new ViewMode($config);
    }

    /**
     * Get view mode by id
     * 
     * @param  int    $id
     * @return ViewMode
     * @throws ViewModeException
     */
    public function getById(int $id): ViewMode
    {
        if ($viewMode = $this->all()->firstWhere('id', $id)) {
            return $viewMode;
        }
        throw ViewModeException::noId($id);
    }

    /**
     * Get all view modes for a layout
     * 
     * @param  Layout $layout
     * @return array
     */
    public function getForLayout(Layout $layout): array
    {
        if (!$layout->id) {
            return [];
        }
        return $this->all()
            ->where('layout.id', $layout->id)
            ->values()
            ->all();
    }

    /**
     * Get a default view mode
     * 
     * @param  Layout $layout
     * @return array
     */
    public function getDefault(Layout $layout): ?ViewMode
    {
        return $this->get($layout);
    }

    /**
     * Get a view mode
     * 
     * @param  Layout $layout
     * @param  string $handle
     * @return array
     */
    public function get(Layout $layout, string $handle = self::DEFAULT_HANDLE): ?ViewMode
    {
        if (!$layout->id) {
            return null;
        }
        return $this->all()
            ->where('layout.id', $layout->id)
            ->firstWhere('handle', $handle);
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

    public function saveMany(array $data, LayoutRecord $layout)
    {
        $ids = [];
        foreach ($data as $viewModesData) {
            $viewMode = $this->getRecordByUid($viewModesData['uid']);
            $viewMode->uid = $viewModesData['uid'];
            $viewMode->handle = $viewModesData['handle'];
            $viewMode->name = $viewModesData['name'];
            $viewMode->layout_id = $layout->id;
            $ids[] = $viewMode->id;
        }
        $toDelete = ViewModeRecord::find()
            ->where(['layout_id' => $layout->id])
            ->andWhere(['not in', 'id', $ids])
            ->all();
        foreach ($toDelete as $viewMode) {
            $viewMode->delete();
        }
        $this->_viewModes = null;
    }
}