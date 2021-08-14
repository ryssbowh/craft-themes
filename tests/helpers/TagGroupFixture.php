<?php 

namespace Ryssbowh\CraftThemesTests\helpers;

use craft\models\TagGroup;
use craft\test\DbFixtureTrait;
use yii\test\DbFixture;
use yii\test\FileFixtureTrait;

abstract class TagGroupFixture extends DbFixture
{
    use FileFixtureTrait;
    use DbFixtureTrait;

    private $_groups = [];

    public function load()
    {
        codecept_debug('Loading tag groups');
        foreach ($this->loadData($this->dataFile) as $key => $data) {
            $group = $this->createGroup($data);

            if (!$this->saveGroup($group)) {
                throw new \Exception(implode(' ', $group->getErrorSummary(true)));
            }

            $this->_groups[$key] = $group;
        }
    }

    public function unload()
    {
        codecept_debug('Unloading tag groups');
        foreach ($this->_groups as $key => $group) {
            $this->deleteGroup($key);
        }
        $this->hardDelete();
    }

    public function deleteGroup($key)
    {
        if (isset($this->_groups[$key])) {
            $group = $this->_groups[$key];
            unset($this->_groups[$key]);
            return \Craft::$app->tags->deleteTagGroup($group);
        }
    }

    public function getGroup(string $key): ?TagGroup
    {
        return $this->_groups[$key] ?? null;
    }

    protected function createGroup(array $data): TagGroup
    {
        return new TagGroup($data);
    }

    protected function saveGroup(TagGroup $group)
    {
        return \Craft::$app->tags->saveTagGroup($group);
    }
}