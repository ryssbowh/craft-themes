<?php 

namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\records\GroupRecord;
use craft\base\MemoizableArray;

class GroupsService extends Service
{
    private $_groups;

    public function all()
    {
        if ($this->_groups === null) {
            $records = GroupRecord::all();
            $this->_groups = collect();
            foreach ($records as $record) {
                $this->_groups->push($this->createGroup($record));
            }
        }
        return $this->_groups;
    }

    public function getRecordByUid(string $uid): ?GroupRecord
    {
        return GroupRecord::find()->where(['uid' => $uid])->one() ?? new GroupRecord;
    }
}