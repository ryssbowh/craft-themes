<?php 

namespace Ryssbowh\CraftThemes\events;

use yii\base\Event;

class LayoutEvent extends Event
{
    /**
     * @var Layout|LayoutRecord
     */
    public $layout;

    /**
     * @var bool
     */
    public $isNew;
}