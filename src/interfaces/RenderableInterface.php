<?php 

namespace Ryssbowh\CraftThemes\interfaces;

use craft\base\Element;

interface RenderableInterface
{
    /**
     * Render for an element
     * 
     * @return string
     */
    public function render(Element $element): string;
}