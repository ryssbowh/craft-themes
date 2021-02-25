<?php 

namespace Ryssbowh\CraftThemes\events;

use yii\base\Event;

class ViewModeEvent extends Event
{
    /**
     * @var ViewMode|ViewModeRecord
     */
    public $viewMode;

    /**
     * @var bool
     */
    public $isNew;
}