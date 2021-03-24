<?php 

namespace Ryssbowh\CraftThemes\events;

use yii\base\Event;

class DisplayEvent extends Event
{
    /**
     * @var Display
     */
    public $display;

    /**
     * @var bool
     */
    public $isNew;
}