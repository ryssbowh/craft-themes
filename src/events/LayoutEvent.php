<?php
namespace Ryssbowh\CraftThemes\events;

use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\records\LayoutRecord;
use yii\base\Event;

class LayoutEvent extends Event
{
    /**
     * @var LayoutInterface|LayoutRecord
     */
    public $layout;

    /**
     * @var bool
     */
    public $isNew;
}