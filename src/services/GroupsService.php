<?php
namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\DisplayInterface;
use Ryssbowh\CraftThemes\models\Group;
use Ryssbowh\CraftThemes\records\DisplayRecord;
use Ryssbowh\CraftThemes\records\GroupPivotRecord;
use Ryssbowh\CraftThemes\records\GroupRecord;
use craft\db\ActiveRecord;
use craft\events\ConfigEvent;
use craft\events\RebuildConfigEvent;
use craft\helpers\StringHelper;

class GroupsService extends Service
{
    const CONFIG_KEY = 'themes.groups';

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
     * @param  DisplayInterface $display
     * @return ?Field
     */
    public function getForDisplay(DisplayInterface $display): ?Group
    {
        return $this->all()->firstWhere('display_id', $display->id);
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
        $config['uid'] = $config['uid'] ?? StringHelper::UUID();
        $group = new Group;
        $group->setAttributes($config);
        return $group;
    }

    /**
     * Saves a group
     * 
     * @param  Group $group
     * @param  bool  $validate
     * @return bool
     */
    public function save(Group $group, bool $validate = true): bool
    {
        if ($validate and !$group->validate()) {
            return false;
        }

        $isNew = !is_int($group->id);
        $uid = $group->uid;

        $projectConfig = \Craft::$app->getProjectConfig();
        $configData = $display->getConfig();
        $configPath = self::CONFIG_KEY . '.' . $uid;
        $projectConfig->set($configPath, $configData);

        $record = $this->getRecordByUid($uid);
        $group->setAttributes($record->getAttributes());
        
        if ($isNew) {
            $this->add($group);
        }

        foreach ($group->displays as $display) {
            Themes::$plugin->displays->save($display);
        }

        return true;
    }

    /**
     * Deletes a group
     * 
     * @param  Group $group
     * @return bool
     */
    public function delete(Group $group): bool
    {
        foreach ($group->displays as $display) {
            $display->group = null;
            Themes::$plugin->displays->save($display);
        }

        \Craft::$app->getProjectConfig()->remove(self::CONFIG_KEY . '.' . $group->uid);

        $this->_groups = $this->all()->where('id', '!=', $group->id);

        return true;
    }

    /**
     * Handles a change in group config
     * 
     * @param ConfigEvent $event
     */
    public function handleChanged(ConfigEvent $event)
    {
        $uid = $event->tokenMatches[0];
        $data = $event->newValue;
        $transaction = \Craft::$app->getDb()->beginTransaction();
        try {
            $group = $this->getRecordByUid($uid);

            $group->display_id = Themes::$plugin->displays->getByUid($data['display_id'])->id;
            $group->name = $data['name'];
            $group->handle = $data['handle'];
            $group->labelHidden = $data['labelHidden'];
            $group->labelVisuallyHidden = $data['labelVisuallyHidden'];
            $group->hidden = $data['hidden'];
            $group->visuallyHidden = $data['visuallyHidden'];
            $group->save(false);
            
            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * Handles a deletion in group config
     * 
     * @param ConfigEvent $event
     */
    public function handleDeleted(ConfigEvent $event)
    {
        $uid = $event->tokenMatches[0];

        \Craft::$app->getDb()->createCommand()
            ->delete(GroupRecord::tableName(), ['uid' => $uid])
            ->execute();
    }

    /**
     * Respond to rebuild config event
     * 
     * @param RebuildConfigEvent $e
     */
    public function rebuildConfig(RebuildConfigEvent $e)
    {
        foreach ($this->all() as $group) {
            $e->config[self::CONFIG_KEY.'.'.$group->uid] = $group->getConfig();
        }
    }

    /**
     * Populates a group from post
     * 
     * @param  array $data
     * @return Group
     */
    public function populateFromPost(array $data): Group
    {
        $group = $this->getById($data['id']);
        $displays = [];
        foreach ($data['displays'] as $displayData) {
            $display = Themes::$plugin->displays->populateFromPost($displayData);
            $display->group = $group;
            $displays[] = $display;
        }
        $data['displays'] = $displays;
        $attributes = $group->safeAttributes();
        $data = array_intersect_key($data, array_flip($attributes));
        $group->setAttributes($data);
        return $group;
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

    /**
     * Add a group to internal cache
     * 
     * @param Group $group
     */
    protected function add(Group $group)
    {
        if (!$this->all()->firstWhere('id', $group->id)) {
            $this->all()->push($group);
        }
    }
}