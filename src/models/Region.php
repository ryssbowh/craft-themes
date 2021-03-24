<?php 

namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\interfaces\RenderableInterface;
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

    public function addBlock(BlockInterface $block)
    {
        $this->blocks[] = $block;
    }

    public function render(): string
    {
        return Themes::$plugin->view->renderRegion($this);
    }

    public function __toString()
    {
        return $this->render();
    }
}