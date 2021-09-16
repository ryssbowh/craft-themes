<?php 

namespace Ryssbowh\CraftThemes\interfaces;

use Ryssbowh\CraftThemes\models\Group;
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
    public function getViewMode(): ViewModeInterface;

    /**
     * View mode setter
     * 
     * @param ViewModeInterface $viewMode
     */
    public function setViewMode(ViewModeInterface $viewMode);

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
     * @return ?Group
     */
    public function getGroup(): ?Group;

    /**
     * Group setter
     * 
     * @param Group $group
     */
    public function setGroup(?Group $group);

    /**
     * Is this display a group 
     * 
     * @return boolean
     */
    public function isGroup(): bool;

    /**
     * Render this display for an element
     * 
     * @param  Element $element
     * @return string
     */
    public function render(Element $element): string;
}