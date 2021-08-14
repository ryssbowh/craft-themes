<?php 

namespace Ryssbowh\CraftThemesTests\helpers;

use craft\models\CategoryGroup;
use craft\models\CategoryGroup_SiteSettings;
use craft\models\Structure;
use craft\test\DbFixtureTrait;
use yii\test\DbFixture;
use yii\test\FileFixtureTrait;

abstract class CategoryGroupFixture extends DbFixture
{
    use FileFixtureTrait;
    use DbFixtureTrait;

    private $_groups = [];

    public function load()
    {
        codecept_debug('Loading category groups');
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
        codecept_debug('Unloading category groups');
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
            return \Craft::$app->categories->deleteGroup($group);
        }
    }

    protected function createGroup(array $data): CategoryGroup
    {
        $structure = new Structure;
        \Craft::$app->structures->saveStructure($structure);
        $allSiteSettings = [];
        foreach (\Craft::$app->getSites()->getAllSites() as $site) {
            $siteSettings = new CategoryGroup_SiteSettings();
            $siteSettings->siteId = $site->id;
            $allSiteSettings[$site->id] = $siteSettings;
        }
        $data['siteSettings'] = $allSiteSettings;
        $data['structureId'] = $structure->id;
        return new CategoryGroup($data);
    }

    protected function saveGroup(CategoryGroup $group)
    {
        return \Craft::$app->categories->saveGroup($group);
    }

    public function getGroup(string $key): ?CategoryGroup
    {
        return $this->_groups[$key] ?? null;
    }
}