<?php
namespace Ryssbowh\CraftThemes\interfaces;

use craft\base\Element;

/**
 * Generic interface for display items.
 * An item belongs to a display, it can be a group or a field.
 */
interface DisplayItemInterface
{
    /**
     * Type getter
     * 
     * @return string
     */
    public static function getType(): string;

    /**
     * Get project vonfig
     * 
     * @return array
     */
    public function getConfig(): array;

    /**
     * Display getter
     * 
     * @return ?DisplayInterface
     */
    public function getDisplay(): DisplayInterface;

    /**
     * Display setter
     * 
     * @param DisplayInterface $display
     */
    public function setDisplay(DisplayInterface $display);

    /**
     * View mode getter
     * 
     * @return ViewModeInterface
     */
    public function getViewMode(): ViewModeInterface;

    /**
     * Layout getter
     * 
     * @return LayoutInterface
     */
    public function getLayout(): LayoutInterface;

    /**
     * Is this item visible
     * 
     * @return boolean
     */
    public function isVisible(): bool;

    /**
     * Eager load fields
     * 
     * @return array
     */
    public function eagerLoad(): array;
}