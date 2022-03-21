<?php

namespace Ryssbowh\CraftThemes\traits;

use Ryssbowh\CraftThemes\models\fieldDisplayers\SuperTableDefault;
use Ryssbowh\CraftThemes\models\fieldDisplayers\SuperTableSlick;
use Ryssbowh\CraftThemes\models\fields\SuperTable as SuperTableField;
use Ryssbowh\CraftThemes\services\FieldDisplayerService;
use Ryssbowh\CraftThemes\services\FieldsService;
use yii\base\Event;

/**
 * @since 3.1.0
 */
trait SuperTable
{
    protected function initSuperTable()
    {
        if (\Craft::$app->plugins->isPluginInstalled('super-table')) {
            $this->_initSuperTable();
        }
    }

    protected function _initSuperTable()
    {
        Event::on(FieldDisplayerService::class, FieldDisplayerService::EVENT_REGISTER_DISPLAYERS, function ($e) {
            $e->registerMany([
                SuperTableSlick::class,
                SuperTableDefault::class
            ]);
        });
        Event::on(FieldsService::class, FieldsService::EVENT_REGISTER_FIELDS, function ($e) {
            $e->registerMany([
                SuperTableField::class
            ]);
        });
    }
}