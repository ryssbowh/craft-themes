<?php 

namespace Ryssbowh\CraftThemes\events;

use yii\base\Event;

class RenderEvent extends Event
{
    /**
     * @var array
     */
    public $templates;

    /**
     * @var array
     */
    public $variables;
}