<?php 

namespace Ryssbowh\CraftThemes\interfaces;

use craft\base\Element;

interface RenderableInterface
{
    /**
     * Get all possible templates
     * 
     * @return array
     */
    public function render(Element $element): string;
}