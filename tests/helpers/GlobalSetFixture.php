<?php 

namespace Ryssbowh\CraftThemesTests\helpers;

use craft\db\Query;
use craft\elements\GlobalSet;
use craft\test\DbFixtureTrait;
use yii\test\DbFixture;
use yii\test\FileFixtureTrait;

abstract class GlobalSetFixture extends DbFixture
{
    use FileFixtureTrait;

    private $_sets = [];

    public function load()
    {
        codecept_debug('Loading globals');
        foreach ($this->loadData($this->dataFile) as $key => $data) {
            $set = $this->createSet($data);

            if (!$this->saveSet($set)) {
                throw new \Exception(implode(' ', $set->getErrorSummary(true)));
            }

            $this->_sets[$key] = $set;
        }
        \Craft::$app->globals->reset();
    }

    public function unload()
    {
        codecept_debug('Unloading globals');
        foreach ($this->_sets as $key => $set) {
            $this->deleteSet($key);
        }
    }

    public function deleteSet($key)
    {
        if (isset($this->_sets[$key])) {
            $global = $this->_sets[$key];
            unset($this->_sets[$key]);
            \Craft::$app->globals->deleteSet($global);
            //Somehow we need to delete the set manually from database, or they won't be deleted
            $this->hardDelete($global->id);
            \Craft::$app->globals->reset();
        }
    }

    public function getSet(string $key): ?GlobalSet
    {
        return $this->_sets[$key] ?? null;
    }

    protected function createSet(array $data): GlobalSet
    {
        return new GlobalSet($data);
    }

    protected function saveSet(GlobalSet $set)
    {
        return \Craft::$app->globals->saveSet($set);
    }

    protected function hardDelete(int $id)
    {
        \Craft::$app->getDb()->createCommand()
            ->delete('globalsets', ['id' => $id])
            ->execute();
    }
}