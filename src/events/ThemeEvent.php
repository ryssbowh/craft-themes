<?php 

namespace Ryssbowh\CraftThemes\events;

use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use yii\base\Event;

class ThemeEvent extends Event
{
    /**
     * @var ThemeInterface
     */
    public $theme;
}