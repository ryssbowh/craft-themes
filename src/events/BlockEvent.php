<?php 

namespace Ryssbowh\CraftThemes\events;

use yii\base\Event;

class BlockEvent extends Event
{
    /**
     * @var BlockInterface|BlockRecord
     */
    public $block;

    /**
     * @var bool
     */
    public $isNew;
}