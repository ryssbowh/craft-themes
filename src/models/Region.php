<?php 

namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\interfaces\RegionInterface;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use craft\base\Element;
use craft\base\Model;

/**
 * Class for a region inside a theme
 */
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
     * @inheritDoc
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
     * @inheritDoc
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
     * @inheritDoc
     */
    public function hasBlocks(): bool
    {
        return sizeof($this->blocks) > 0;
    }

    /**
     * @inheritDoc
     */
    public function getVisibleBlocks(): array
    {
        return array_filter($this->blocks, function ($block) {
            return $block->isVisible();
        });
    }

    /**
     * @inheritDoc
     */
    public function getTheme(): ThemeInterface
    {
        return $this->layout->theme;
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    public function getTemplates(LayoutInterface $layout): array
    {
        $type = $layout->type;
        return [
            'regions/' . $type . '/' . $layout->getTemplatingKey() . '/region-' . $this->handle,
            'regions/' . $type . '/' . $layout->getTemplatingKey() . '/region',
            'regions/' . $type . '/region-' . $this->handle,
            'regions/' . $type . '/region',
            'regions/region-' . $this->handle, 
            'regions/region'
        ];
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return Themes::$plugin->view->renderRegion($this);
    }
}