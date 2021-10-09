<?php 

namespace Ryssbowh\CraftThemes\events;

use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use yii\base\Event;

class InstallThemeEvent extends Event
{
    /**
     * @var ThemeInterface
     */
    public $theme;
}