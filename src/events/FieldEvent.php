<?php 

namespace Ryssbowh\CraftThemes\events;

use yii\base\Event;

class FieldEvent extends Event
{
    /**
     * @var Field|FieldRecord
     */
    public $field;

    /**
     * @var bool
     */
    public $isNew;
}