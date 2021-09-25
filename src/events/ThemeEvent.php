<?php 

namespace Ryssbowh\CraftThemes\events;

use yii\base\Event;

class ThemeEvent extends Event
{
    /**
     * @var ?ThemeInterface
     */
    public $theme;
}