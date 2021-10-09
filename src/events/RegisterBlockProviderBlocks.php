<?php 

namespace Ryssbowh\CraftThemes\events;

use Ryssbowh\CraftThemes\interfaces\BlockProviderInterface;
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