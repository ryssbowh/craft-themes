<?php
namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\interfaces\BlockInterface;

/**
 * Default class for block options
 */
class BlockOptions extends EventDefinableOptions
{
    /**
     * @var BlockInterface
     */
    protected $_block;

    /**
     * Block getter
     * 
     * @return BlockInterface
     */
    public function getBlock(): BlockInterface
    {
        return $this->_block;
    }

    /**
     * Block setter
     * 
     * @param BlockInterface $block
     */
    public function setBlock(BlockInterface $block)
    {
        $this->_block = $block;
    }

    /**
     * @inheritDoc
     */
    protected function reservedWords(): array
    {
        return array_merge(parent::reservedWords(), ['block']);
    }
}