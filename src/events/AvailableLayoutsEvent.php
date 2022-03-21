<?php
namespace Ryssbowh\CraftThemes\events;

use yii\base\Event;

/**
 * Event to register available layouts
 *
 * @since 3.1.0
 */
class AvailableLayoutsEvent extends Event
{   
    /**
     * @var LayoutInterface[]
     */
    public $layouts;

    /**
     * @var string
     */
    public $themeHandle;
}