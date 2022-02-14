<?php
namespace Ryssbowh\CraftThemes\interfaces;

/**
 * A block provider, can define several blocks. Their blocks can be modified through events.
 */
interface BlockProviderInterface 
{
    const EVENT_REGISTER_BLOCKS = 'register_blocks';

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
     * @return BlockInterface[]
     */
    public function getBlocks(): array;

    /**
     * Get all defined blocks classes
     * 
     * @return string[]
     */
    public function getDefinedBlocks(): array;
}