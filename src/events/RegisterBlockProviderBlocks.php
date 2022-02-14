<?php
namespace Ryssbowh\CraftThemes\events;

use Ryssbowh\CraftThemes\interfaces\BlockProviderInterface;
use yii\base\Event;

class RegisterBlockProviderBlocks extends Event
{
    /**
     * @var string[]
     */
    public $blocks;

    /**
     * @var BlockProviderInterface
     */
    public $provider;
}