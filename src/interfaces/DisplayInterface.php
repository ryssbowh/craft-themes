<?php 

namespace Ryssbowh\CraftThemes\interfaces;

use craft\base\Element;

interface DisplayInterface 
{
    /**
     * Get project config
     * 
     * @return array
     */
    public function getConfig(): array;

    /**
     * Layout getter
     * 
     * @return LayoutInterface
     */
    public function getLayout(): LayoutInterface;

    /**
     * View mode interface
     * 
     * @return ViewModeInterface
     */
    public function getViewMode(): ?ViewModeInterface;

    /**
     * View mode setter
     * 
     * @param ViewModeInterface $viewMode
     */
    public function setViewMode(?ViewModeInterface $viewMode);

    /**
     * Item getter
     * 
     * @return DisplayItemInterface
     */
    public function getItem(): DisplayItemInterface;

    /**
     * Item setter
     * 
     * @param DisplayItemInterface $item
     */
    public function setItem(DisplayItemInterface $item);

    /**
     * Group getter
     * 
     * @return ?GroupInterface
     */
    public function getGroup(): ?GroupInterface;

    /**
     * Group setter
     * 
     * @param GroupInterface $group
     */
    public function setGroup(?GroupInterface $group);

    /**
     * Is this display a group 
     * 
     * @return boolean
     */
    public function isGroup(): bool;

    /**
     * Render this display
     * 
     * @return string
     */
    public function render(): string;
}