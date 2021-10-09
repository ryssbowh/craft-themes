<?php

namespace Ryssbowh\CraftThemes\interfaces;

/**
 * A view mode has displays associated to it, it's associated to a layout.
 * One layout can have several view modes.
 */
interface ViewModeInterface
{
    /**
     * Get project config 
     * 
     * @return array
     */
    public function getConfig(): array;

    /**
     * Displays getter
     * 
     * @return array
     */
    public function getDisplays(): array;

    /**
     * Display setter
     * 
     * @param ?array $displays
     */
    public function setDisplays(?array $displays);

    /**
     * Get all visible displays 
     * 
     * @return array
     */
    public function getVisibleDisplays(): array;

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