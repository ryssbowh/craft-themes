<?php 

namespace Ryssbowh\CraftThemes\events;

use yii\base\Event;

class BlockEvent extends Event
{
	public $block;
	public $isNew;
}