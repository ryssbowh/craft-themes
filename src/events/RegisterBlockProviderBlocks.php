<?php 

namespace Ryssbowh\CraftThemes\events;

use yii\base\Event;

class RegisterBlockProviderBlocks extends Event
{
    /**
     * @var array
     */
    public $blocks;

    /**
     * @var BlockProviderInterface
     */
    public $provider;
}