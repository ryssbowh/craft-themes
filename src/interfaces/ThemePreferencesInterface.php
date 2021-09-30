<?php 

namespace Ryssbowh\CraftThemes\interfaces;

interface ThemePreferencesInterface 
{
    /**
     * Layout classes getter
     * 
     * @param  LayoutInterface   $layout
     * @param  boolean           $root true if we're rendering a page template
     * @return array
     */
    public function getLayoutClasses(LayoutInterface $layout, bool $root = false): array;

    /**
     * Get layout attributes
     * 
     * @param  LayoutInterface   $layout
     * @param  boolean           $root true if we're rendering a page template
     * @return array
     */
    public function getLayoutAttributes(LayoutInterface $layout, bool $root = false): array;
}