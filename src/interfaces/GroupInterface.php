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
     * Get available templates
     * 
     * @param  LayoutInterface   $layout
     * @param  ViewModeInterface $viewMode
     * @return array
     */
    public function getTemplates(LayoutInterface $layout, ViewModeInterface $viewMode): array;

    /**
     * Callback before rendering, returning false will skip the group rendering
     * 
     * @return bool;
     */
    public function beforeRender(): bool;

    /**
     * Render this group
     * 
     * @return string
     */
    public function render(): string;
}