<?php 

namespace Ryssbowh\CraftThemesTests\helpers;

use craft\base\Volume;
use craft\test\DbFixtureTrait;
use yii\test\DbFixture;
use yii\test\FileFixtureTrait;

abstract class VolumeFixture extends DbFixture
{
    use FileFixtureTrait;
    use DbFixtureTrait;

    private $_volumes = [];

    public function load()
    {
        codecept_debug('Loading volumes');
        foreach ($this->loadData($this->dataFile) as $key => $data) {
            $volume = $this->createVolume($data);

            if (!$this->saveVolume($volume)) {
                throw new \Exception(implode(' ', $volume->getErrorSummary(true)));
            }

            $this->_volumes[$key] = $volume;
        }
    }

    public function unload()
    {
        codecept_debug('Unloading volumes');
        foreach ($this->_volumes as $key => $volume) {
            $this->deleteVolume($key);
        }
        $this->hardDelete();
    }

    public function deleteVolume($key)
    {
        if (isset($this->_volumes[$key])) {
            $volume = $this->_volumes[$key];
            unset($this->_volumes[$key]);
            return \Craft::$app->volumes->deleteVolume($volume);
        }
    }

    public function getVolume(string $key): ?Volume
    {
        return $this->_volumes[$key] ?? null;
    }

    protected function createVolume(array $data): Volume
    {
        return \Craft::$app->volumes->createVolume($data);
    }

    protected function saveVolume(Volume $volume)
    {
        return \Craft::$app->volumes->saveVolume($volume);
    }
}