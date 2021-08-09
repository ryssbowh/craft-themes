<?php 

namespace Ryssbowh\CraftThemes\events;

use yii\base\Event;

class UninstallThemeEvent extends Event
{
    /**
     * @var ThemeInterface
     */
    public $theme;
}