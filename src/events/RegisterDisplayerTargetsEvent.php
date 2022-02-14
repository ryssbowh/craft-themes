<?php
namespace Ryssbowh\CraftThemes\events;

use yii\base\Event;

class RegisterDisplayerTargetsEvent extends Event
{   
    /**
     * @var array
     */
    public $targets = [];
}