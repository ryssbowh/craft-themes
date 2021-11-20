<?php
namespace Ryssbowh\CraftThemes\events;

use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\records\BlockRecord;
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