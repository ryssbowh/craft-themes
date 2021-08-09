<?php 

namespace Ryssbowh\CraftThemes\events;

use yii\base\Event;

class InstallThemeEvent extends Event
{
    /**
     * @var ThemeInterface
     */
    public $theme;
}