<?php 

namespace Ryssbowh\CraftThemes\events;

use Ryssbowh\CraftThemes\interfaces\DisplayInterface;
use Ryssbowh\CraftThemes\records\DisplayRecord;
use yii\base\Event;

class DisplayEvent extends Event
{
    /**
     * @var DisplayInterface|DisplayRecord
     */
    public $display;

    /**
     * @var bool
     */
    public $isNew;
}