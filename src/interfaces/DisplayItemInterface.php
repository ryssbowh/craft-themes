<?php 

namespace Ryssbowh\CraftThemes\interfaces;

use Ryssbowh\CraftThemes\models\ViewMode;

interface DisplayItemInterface extends RenderableInterface
{
    /**
     * Get project vonfig
     * 
     * @return array
     */
    public function getConfig(): array;

    /**
     * Display getter
     * 
     * @return ?DisplayInterface
     */
    public function getDisplay(): ?DisplayInterface;

    /**
     * Display setter
     * 
     * @param DisplayInterface $display
     */
    public function setDisplay(DisplayInterface $display);

    /**
     * View mode getter
     * 
     * @return ViewMode
     */
    public function getViewMode(): ViewMode;

    /**
     * Layout getter
     * 
     * @return LayoutInterface
     */
    public function getLayout(): LayoutInterface;
}