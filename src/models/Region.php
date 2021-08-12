<?php 

namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\interfaces\RegionInterface;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use craft\base\Element;
use craft\base\Model;

class Region extends Model implements RegionInterface
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
     * @var LayoutInterface
     */
    public $layout;

    /**
     * @var array
     */
    protected $_blocks;

    /**
     * Blocks getter
     * 
     * @return array
     */
    public function getBlocks(): array
    {
        if (is_null($this->_blocks)) {
            if ($this->theme->isPartial()) {
                $this->_blocks = [];
            } else if (!$this->layout->hasBlocks) {
                $defaultLayout = Themes::$plugin->layouts->getDefault($this->layout->theme);
                $this->_blocks = $defaultLayout ? $defaultLayout->getRegion($this->handle)->blocks : [];
            } else {
                $this->_blocks = Themes::$plugin->blocks->getForRegion($this);
            }
        }
        return $this->_blocks;
    }

    /**
     * Blocks setter
     * 
     * @param array $blocks
     */
    public function setBlocks(?array $blocks)
    {
        if (is_array($blocks)) {
            foreach ($blocks as $block) {
                $block->region = $this->handle;
                $block->layout = $this->layout;
            }
        }
        $this->_blocks = $blocks;
    }

    /**
     * Theme getter
     * 
     * @return ThemeInterface
     */
    public function getTheme(): ThemeInterface
    {
        return $this->layout->theme;
    }

    /**
     * Add a block to this region
     * 
     * @param BlockInterface $block
     */
    public function addBlock(BlockInterface $block)
    {
        $block->region = $this->handle;
        $block->layout = $this->layout;
        $blocks = $this->blocks;
        $blocks[] = $block;
        $this->_blocks = $blocks;
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