<?php
namespace Ryssbowh\CraftThemes\interfaces;

use craft\base\Element;

/**
 * A display is assigned to a view mode, it has an `item` which can be a field or a group
 */
interface DisplayInterface 
{
    /**
     * Get project config
     * 
     * @return array
     */
    public function getConfig(): array;

    /**
     * Get the display's item handle
     * 
     * @return string
     */
    public function getHandle(): string;

    /**
     * Get the display's item name
     * 
     * @return string
     */
    public function getName(): string;

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
     * @param  array $params Parameters forwarded to display's item render method
     * @return string
     */
    public function render(array $params = []): string;
}