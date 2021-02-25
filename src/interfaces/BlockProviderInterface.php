<?php 

namespace Ryssbowh\CraftThemes\interfaces;

use Ryssbowh\CraftThemes\interfaces\BlockInterface;

interface BlockProviderInterface 
{
    /**
     * Add a block to this provider
     * 
     * @param  string $blockClass
     * @return BlockProviderInterface
     */
    public function addBlock(string $blockClass): BlockProviderInterface;

    /**
     * Get a block instance
     * 
     * @param  string $handle
     * @param  array  $attributes
     * @return BlockInterface
     */
    public function getBlock(string $handle, array $attributes = []): BlockInterface;

    /**
     * Get all defined blocks as objects
     * 
     * @return array
     */
    public function getBlocksObjects(): array;
}