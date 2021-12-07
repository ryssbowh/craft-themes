<?php
namespace Ryssbowh\CraftThemes\events;

use yii\base\Event;

class DefinableOptionsDefinitions extends Event
{
    /**
     * @var array
     */
    public $definitions;

    /**
     * @var array
     */
    public $defaultValues;
}