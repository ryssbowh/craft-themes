<?php
namespace Ryssbowh\CraftThemes\interfaces;

/**
 * A group is a type of item, it can contains several displays
 */
interface GroupInterface extends HasDisplaysInterface
{
    /**
     * Get available templates
     * 
     * @return array
     */
    public function getTemplates(): array;

    /**
     * Callback before rendering, returning false will skip the group rendering
     * 
     * @return bool
     */
    public function beforeRender(): bool;

    /**
     * Render this group
     * 
     * @return string
     */
    public function render(): string;
}