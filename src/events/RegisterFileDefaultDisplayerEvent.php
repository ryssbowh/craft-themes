<?php
namespace Ryssbowh\CraftThemes\events;

use yii\base\Event;

class RegisterFileDefaultDisplayerEvent extends Event
{
    /**
     * @var string[]
     */
    public $defaults = [];
}