<?php 

namespace Ryssbowh\CraftThemes\interfaces;

use Ryssbowh\CraftThemes\interfaces\BlockProviderInterface;
use Ryssbowh\CraftThemes\models\layouts\Layout;
use craft\base\Model;

interface BlockInterface extends RenderableInterface
{
    /**
     * Get block handle
     * 
     * @return string
     */
    public function getHandle(): string;

    /**
     * Get options model
     * 
     * @return Model
     */
    public function getOptions(): Model;

    /**
     * Block settings html
     * 
     * @return string
     */
    public function getOptionsHtml(): string;

    /**
     * Get full machine name
     * 
     * @return string
     */
    public function getMachineName(): string;

    /**
     * Model that defines this block's options 
     * 
     * @return Model
     */
    public function getOptionsModel(): Model;

    /**
     * Get layout object
     * 
     * @return Layout
     */
    public function layout(): Layout;

    /**
     * Get provider object
     * 
     * @return BlockProviderInterface
     */
    public function provider(): BlockProviderInterface;
}