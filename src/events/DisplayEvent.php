<?php 

namespace Ryssbowh\CraftThemes\events;

use Ryssbowh\CraftThemes\interfaces\DisplayInterface;
use yii\base\Event;

class DisplayEvent extends Event
{
    /**
     * @var DisplayInterface
     */
    public $display;

    /**
     * @var bool
     */
    public $isNew;
}