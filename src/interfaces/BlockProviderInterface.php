<?php 

namespace Ryssbowh\CraftThemes\interfaces;

interface BlockProviderInterface 
{
    public function getHandle(): string;

    public function getName(): string;

    /**
     * Get a block instance
     * 
     * @param  string $handle
     * @param  array  $attributes
     * @return BlockInterface
     */
    public function createBlock(string $handle): BlockInterface;

    /**
     * Get all defined blocks as objects
     * 
     * @return array
     */
    public function getBlocks(): array;

    public function getDefinedBlocks(): array;

    public function addDefinedBlock(string $blockClass): BlockProviderInterface;
}