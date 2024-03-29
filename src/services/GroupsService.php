<?php
namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\helpers\ProjectConfigHelper;
use Ryssbowh\CraftThemes\interfaces\DisplayInterface;
use Ryssbowh\CraftThemes\interfaces\GroupInterface;
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
    public function getAll()
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
     * Get group by id
     * 
     * @param  int $id
     * @return Group
     * @throws GroupException
     */
    public function getById(int $id): GroupInterface
    {
        if ($group = $this->getAll()->firstWhere('id', $id)) {
            return $group;
        }
        throw GroupException::noId($id);
    }

    /**
     * Get a group by uid
     * 
     * @param  int $uid
     * @return ?GroupInterface
     */
    public function getByUid(string $uid): ?GroupInterface
    {
        return $this->getAll()->firstWhere('uid', $uid);
    }

    /**
     * Get a group for a display
     * 
     * @param  DisplayInterface $display
     * @return ?Field
     */
    public function getForDisplay(DisplayInterface $display): ?GroupInterface
    {
        return $this->getAll()->firstWhere('display_id', $display->id);
    }

    /**
     * Create a group from config
     * 
     * @param  array|ActiveRecord $config
     * @return GroupInterface
     */
    public function create($config): GroupInterface
    {
        if ($config instanceof ActiveRecord) {
            $config = $config->getAttributes();
        }
        $displayData = null;
        if (isset($config['displays'])) {
            $displayData = $config['displays'];
            unset($config['displays']);
        }
        $group = new Group;
        $group->setAttributes($config);
        if ($displayData) {
            $displays = [];
            foreach ($displayData as $data) {
                $displays[] = $this->displayService()->create($data);
            }
            $group->displays = $displays;
        }
        return $group;
    }

    /**
     * Saves a group
     * 
     * @param  GroupInterface $group
     * @param  bool  $validate
     * @return bool
     */
    public function save(GroupInterface $group, bool $validate = true): bool
    {
        if ($validate and !$group->validate()) {
            return false;
        }

        $isNew = !is_int($group->id);

        $projectConfig = \Craft::$app->getProjectConfig();
        $configData = $group->getConfig();
        $uid = $group->uid ?? StringHelper::UUID();
        $configPath = self::CONFIG_KEY . '.' . $uid;
        $projectConfig->set($configPath, $configData);

        $record = $this->getRecordByUid($uid);
        $group->setAttributes($record->getAttributes());

        foreach ($group->displays as $display) {
            $display->group_id = $group->id;
            Themes::$plugin->displays->save($display);
            $display->group = $group;
        }

        if ($isNew) {
            $this->add($group);
        }

        return true;
    }

    /**
     * Deletes a group
     * 
     * @param  GroupInterface $group
     * @return bool
     */
    public function delete(GroupInterface $group): bool
    {
        //Assigning all display of the group to the group's view mode
        foreach ($group->displays as $display) {
            $display->group = null;
            $display->viewMode = $group->viewMode;
            Themes::$plugin->displays->save($display);
        }

        \Craft::$app->projectConfig->remove(self::CONFIG_KEY . '.' . $group->uid);

        $this->_groups = $this->getAll()->where('id', '!=', $group->id);

        return true;
    }

    /**
     * Handles a change in group config
     * 
     * @param ConfigEvent $event
     */
    public function handleChanged(ConfigEvent $event)
    {
        ProjectConfigHelper::ensureAllDisplaysProcessed();
        $uid = $event->tokenMatches[0];
        $data = $event->newValue;
        if (!$data) {
            //This can happen when fixing broken states
            return;
        }
        $transaction = \Craft::$app->getDb()->beginTransaction();
        try {
            $group = $this->getRecordByUid($uid);

            $group->display_id = Themes::$plugin->displays->getRecordByUid($data['display_id'])->id;

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
        $parts = explode('.', self::CONFIG_KEY);
        foreach ($this->getAll() as $group) {
            $e->config[$parts[0]][$parts[1]][$group->uid] = $group->getConfig();
        }
    }

    /**
     * Populates a group from an array of data
     * 
     * @param  array $data
     * @return GroupInterface
     */
    public function populateFromData(array $data): GroupInterface
    {
        $displaysData = $data['displays'] ?? [];
        unset($data['displays']);
        if ($data['id'] ?? null) {
            $group = $this->getById($data['id']);
            $attributes = $group->safeAttributes();
            $data = array_intersect_key($data, array_flip($attributes));
            $group->setAttributes($data);
        } else {
            $group = $this->create($data);
        }
        $group->displays = array_map(function ($data) use ($group) {
            $display = Themes::$plugin->displays->populateFromData($data);
            $display->group = $group;
            return $display;
        }, $displaysData);
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
     * @param GroupInterface $group
     */
    protected function add(GroupInterface $group)
    {
        if (!$this->getAll()->firstWhere('id', $group->id)) {
            $this->getAll()->push($group);
        }
    }
}