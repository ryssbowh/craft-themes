<?php

namespace Ryssbowh\CraftThemes\traits;

use Ryssbowh\CraftThemes\models\fieldDisplayers\NeoDefault;
use Ryssbowh\CraftThemes\models\fields\NeoField;
use Ryssbowh\CraftThemes\services\FieldDisplayerService;
use Ryssbowh\CraftThemes\services\FieldsService;
use yii\base\Event;

/**
 * Integrates with super table plugin
 * 
 * @since 3.2.0
 */
trait Neo
{
    protected function initNeo()
    {
        if (\Craft::$app->plugins->isPluginEnabled('neo')) {
            $this->_initNeo();
        }
    }

    protected function _initNeo()
    {
        Event::on(FieldDisplayerService::class, FieldDisplayerService::EVENT_REGISTER_DISPLAYERS, function (Event $e) {
            $e->registerMany([
                NeoDefault::class
            ]);
        });
        Event::on(FieldsService::class, FieldsService::EVENT_REGISTER_FIELDS, function (Event $e) {
            $e->registerMany([
                NeoField::class
            ]);
        });
    }
}