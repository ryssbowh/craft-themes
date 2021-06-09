<?php 

namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\interfaces\RenderableInterface;
use craft\base\Element;
use craft\base\Model;

class Region extends Model implements RenderableInterface
{   
    /**
     * @var string
     */
    public $handle = '';

    /**
     * @var string
     */
    public $name = '';

    /**
     * @var string
     */
    public $width = '100%';

    /**
     * @var array
     */
    public $blocks = [];

    /**
     * Add a block to this region
     * 
     * @param BlockInterface $block
     */
    public function addBlock(BlockInterface $block)
    {
        $this->blocks[] = $block;
    }

    /**
     * Render this region for an element
     * 
     * @param  Element $element
     * @return string
     */
    public function render(Element $element): string
    {
        return Themes::$plugin->view->renderRegion($this, $element);
    }
}