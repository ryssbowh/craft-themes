<?php
namespace Ryssbowh\CraftThemes\interfaces;

use Ryssbowh\CraftThemes\interfaces\FieldInterface;

/**
 * A view mode has displays associated to it, it's associated to a layout.
 * One layout can have several view modes.
 */
interface ViewModeInterface extends HasDisplaysInterface
{
    /**
     * Get project config 
     * 
     * @return array
     */
    public function getConfig(): array;

    /**
     * Add a display to this view mode
     * 
     * @param DisplayInterface $display
     */
    public function addDisplay(DisplayInterface $display);

    /**
     * Get layout object
     * 
     * @return LayoutInterface
     */
    public function getLayout(): LayoutInterface;

    /**
     * Layout setter
     * 
     * @param LayoutInterface $layout
     */
    public function setLayout(LayoutInterface $layout);
}