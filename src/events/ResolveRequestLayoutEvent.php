<?php
namespace Ryssbowh\CraftThemes\events;

use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use craft\base\Element;
use yii\base\Event;

class ResolveRequestLayoutEvent extends Event
{
    /**
     * @var Element
     */
    public $element;

    /**
     * @var LayoutInterface|null
     */
    public $layout;
}