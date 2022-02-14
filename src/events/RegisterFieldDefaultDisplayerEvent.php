<?php
namespace Ryssbowh\CraftThemes\events;

use yii\base\Event;

class RegisterFieldDefaultDisplayerEvent extends Event
{
    /**
     * @var string
     */
    public $default = '';
}