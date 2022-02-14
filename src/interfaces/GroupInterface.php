<?php
namespace Ryssbowh\CraftThemes\interfaces;

use Twig\Markup;

/**
 * A group is a type of item, it can contains several displays
 */
interface GroupInterface extends HasDisplaysInterface
{
    /**
     * Get available templates
     * 
     * @return string[]
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
     * @return Markup
     */
    public function render(): Markup;
}