<?php
namespace Ryssbowh\CraftThemes\interfaces;

/**
 * A group is a type of item, it can contains several displays
 */
interface GroupInterface
{
    /**
     * Displays getter
     * 
     * @return array
     */
    public function getDisplays(): array;

    /**
     * Displays setter
     * 
     * @param ?array $displays
     */
    public function setDisplays(?array $displays);

    /**
     * Visible displays getter
     * 
     * @return array
     */
    public function getVisibleDisplays(): array;

    /**
     * Render this group
     * 
     * @return string
     */
    public function render(): string;
}