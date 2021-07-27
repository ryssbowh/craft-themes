<?php
namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\DisplayInterface;
use Ryssbowh\CraftThemes\models\Group;
use Ryssbowh\CraftThemes\records\DisplayRecord;
use Ryssbowh\CraftThemes\records\GroupPivotRecord;
use Ryssbowh\CraftThemes\records\GroupRecord;
use craft\db\ActiveRecord;

class GroupsService extends Service
{
    /**
     * @var Collection
     */
    private $_groups;

    /**
     * Get all groups
     * 
     * @return Collection
     */
    public function all()
    {
        if ($this->_groups === null) {
            $records = GroupRecord::find()->all();
            $this->_groups = collect();
            foreach ($records as $record) {
                $this->_groups->push($this->create($record));
            }
        }
        return $this->_groups;
    }

    /**
     * Get a group for a display
     * 
     * @param  int    $displayId
     * @return ?Field
     */
    public function getForDisplay(int $displayId): ?Group
    {
        return $this->all()->firstWhere('display_id', $displayId);
    }

    /**
     * Create a group from config
     * 
     * @param  array|ActiveRecord $config
     * @return Group
     */
    public function create($config): Group
    {
        if ($config instanceof ActiveRecord) {
            $config = $config->getAttributes();
        }
        $group = new Group;
        $group->setAttributes($config);
        return $group;
    }

    /**
     * Saves a group's data 
     * 
     * @param  array         $data
     * @param  DisplayRecord $display
     * @return bool
     */
    public function save(array $data, DisplayRecord $display): bool
    {
        $data['display_id'] = $display->id;
        $group = $this->getRecordByUid($data['uid']);
        $group->setAttributes($data, false);
        return $group->save(false);
    }

    /**
     * Get a group record by uid or a new one if it doesn't exist
     * 
     * @param  string $uid
     * @return GroupRecord
     */
    public function getRecordByUid(string $uid): GroupRecord
    {
        return GroupRecord::find()->where(['uid' => $uid])->one() ?? new GroupRecord(['uid' => $uid]);
    }
}