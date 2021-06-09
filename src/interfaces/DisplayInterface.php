<?php 

namespace Ryssbowh\CraftThemes\interfaces;

use Ryssbowh\CraftThemes\models\ViewMode;
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
    public function getViewMode(): ViewMode;

    /**
     * View mode setter
     * 
     * @param ViewModeInterface $viewMode
     */
    public function setViewMode(ViewMode $viewMode);

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
    public function setItem(?DisplayItemInterface $item);

    /**
     * Render this display for an element
     * 
     * @param  Element $element
     * @return string
     */
    public function render(Element $element): string;
}