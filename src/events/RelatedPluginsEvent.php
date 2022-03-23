<?php
namespace Ryssbowh\CraftThemes\events;

use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\records\BlockRecord;
use yii\base\Event;

/**
 * Event to register plugins related to themes
 * 
 * @since  3.1.0
 */
class RelatedPluginsEvent extends Event
{
    /**
     * @var array
     */
    protected $_related = ['super-table', 'commerce', 'typedlinkfield'];

    /**
     * Add a related plugin's handle
     * 
     * @param string $handle
     */
    public function add(string $handle)
    {
        if (!in_array($handle, $this->_related)) {
            $this->_related[] = $handle;
        }
    }

    /**
     * Get all related plugins handles
     * 
     * @return array
     */
    public function getRelated(): array
    {
        return $this->_related;
    }
}