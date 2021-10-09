<?php 

namespace Ryssbowh\CraftThemes\events;

use Ryssbowh\CraftThemes\blockCache\GlobalBlockCache;
use Ryssbowh\CraftThemes\blockCache\PathBlockCache;
use Ryssbowh\CraftThemes\blockCache\QueryBlockCache;
use Ryssbowh\CraftThemes\interfaces\BlockCacheStrategyInterface;
use yii\base\Event;

class RegisterBlockCacheStrategies extends Event
{
    /**
     * @var array
     */
    protected $_strategies;

    public function init()
    {
        parent::init();
        $this->add(new GlobalBlockCache);
        $this->add(new PathBlockCache);
        $this->add(new QueryBlockCache);
    }

    /**
     * Add a strategy, will replace strategies with same handle
     * 
     * @param BlockCacheStrategyInterface $strategy
     */
    public function add(BlockCacheStrategyInterface $strategy)
    {
        $this->_strategies[$strategy->handle] = $strategy;
    }

    /**
     * Get all strategies
     * 
     * @return array
     */
    public function getStrategies(): array
    {
        return $this->_strategies;
    }
}