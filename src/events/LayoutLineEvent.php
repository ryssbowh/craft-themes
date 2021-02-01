<?php 

namespace Ryssbowh\CraftThemes\events;

use yii\base\Event;

class LayoutLineEvent extends Event
{
	public $line;
	public $isNew;
}