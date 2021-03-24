<?php 

namespace Ryssbowh\CraftThemes\interfaces;

interface RenderableInterface
{
    /**
     * Get all possible templates
     * 
     * @return array
     */
    public function render(): string;
}