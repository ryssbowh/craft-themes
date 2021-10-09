<?php
namespace Ryssbowh\CraftThemes\interfaces;

use Ryssbowh\CraftThemes\interfaces\BlockInterface;

/**
 * Class that represents the options for a block
 */
interface BlockOptionsInterface
{   
    /**
     * Get project config array
     * 
     * @return array
     */
    public function getConfig(): array;

    /**
     * Callback after a block has been saved
     * 
     * @param BlockInterface $block
     */
    public function afterSave(BlockInterface $block);
}