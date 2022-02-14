<?php
namespace Ryssbowh\CraftThemes\events;

use Ryssbowh\CraftThemes\interfaces\ViewModeInterface;
use Ryssbowh\CraftThemes\records\ViewModeRecord;
use yii\base\Event;

class ViewModeEvent extends Event
{
    /**
     * @var ViewModeInterface|ViewModeRecord
     */
    public $viewMode;

    /**
     * @var bool
     */
    public $isNew;
}