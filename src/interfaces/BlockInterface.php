<?php 

namespace Ryssbowh\CraftThemes\interfaces;

use Ryssbowh\CraftThemes\interfaces\BlockProviderInterface;

/**
 * Defines a block. Blocks are defined by providers, they can have various options
 * and can be assigned to theme's regions.
 */
interface BlockInterface
{
    /**
     * Name getter
     * 
     * @return string
     */
    public function getName(): string;

    /**
     * Is this block visible
     */
    public function isVisible(): bool;
    
    /**
     * Small description getter
     * 
     * @return string
     */
    public function getSmallDescription(): string;
    /**
     * Long description getter
     * 
     * @return string
     */
    public function getLongDescription(): string;

    /**
     * Get block handle
     * 
     * @return string
     */
    public function getHandle(): string;

    /**
     * Get options model
     * 
     * @return BlockOptionsInterface
     */
    public function getOptions(): BlockOptionsInterface;

    /**
     * Options setter
     * 
     * @param string|array $options
     */
    public function setOptions($options);

    /**
     * Get full machine name, in the form provider-handle
     * 
     * @return string
     */
    public function getMachineName(): string;

    /**
     * Model that defines this block's options 
     * 
     * @return BlockOptionsInterface
     */
    public function getOptionsModel(): BlockOptionsInterface;

    /**
     * Get layout object
     * 
     * @return LayoutInterface
     */
    public function getLayout(): ?LayoutInterface;

    /**
     * Set layout object
     * 
     * @param LayoutInterface $layout
     */
    public function setLayout(LayoutInterface $layout);

    /**
     * Get provider object
     * 
     * @return BlockProviderInterface
     */
    public function provider(): BlockProviderInterface;

    /**
     * Project config to be saved
     * 
     * @return array
     */
    public function getConfig(): array;

    /**
     * Callback after the block has been saved
     */
    public function afterSave();

    /**
     * Get available templates
     * 
     * @param  LayoutInterface $layout
     * @param  RegionInterface $region
     * @return array
     */
    public function getTemplates(LayoutInterface $layout, RegionInterface $region): array;

    /**
     * Render this block
     * 
     * @return string
     */
    public function render(): string;
}