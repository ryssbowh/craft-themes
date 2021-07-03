<?php 

namespace Ryssbowh\CraftThemes\interfaces;

interface BlockProviderInterface 
{
    const REGISTER_BLOCKS = 'register_blocks';

    /**
     * Handle getter
     * 
     * @return string
     */
    public function getHandle(): string;

    /**
     * Name getter 
     * 
     * @return string
     */
    public function getName(): string;

    /**
     * Get a block instance
     * 
     * @param  string $handle
     * @return BlockInterface
     */
    public function createBlock(string $handle): BlockInterface;

    /**
     * Get all defined blocks as objects
     * 
     * @return array
     */
    public function getBlocks(): array;

    /**
     * Get all defined blocks
     * 
     * @return array
     */
    public function getDefinedBlocks(): array;
}