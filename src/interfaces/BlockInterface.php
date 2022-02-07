<?php
namespace Ryssbowh\CraftThemes\interfaces;

use Ryssbowh\CraftThemes\interfaces\BlockProviderInterface;
use Ryssbowh\CraftThemes\models\BlockOptions;

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
     *
     * @return bool
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
     * @return BlockOptions
     */
    public function getOptions(): BlockOptions;

    /**
     * Options setter
     * 
     * @param string|array $options
     */
    public function setOptions($options);

    /**
     * Can this block be cached
     * 
     * @return bool
     */
    public function getCanBeCached(): bool;

    /**
     * Set the cache strategy, $strategy should be an array : 
     * [
     *     'handle' => 'strategyHandle',
     *     'options' => []
     * ]
     * @param array $strategy
     */
    public function setCacheStrategy($strategy);

    /**
     * Get the cache strategy for this block
     * 
     * @return ?BlockCacheStrategyInterface
     */
    public function getCacheStrategy(): ?BlockCacheStrategyInterface;

    /**
     * Get full machine name, in the form provider-handle
     * 
     * @return string
     */
    public function getMachineName(): string;

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
     * @param  LayoutInterface
     * @return string[]
     */
    public function getTemplates(LayoutInterface $layout): array;

    /**
     * Callback before rendering, returning false will skip the block rendering.
     *
     * @return bool
     */
    public function beforeRender(): bool;

    /**
     * Render this block
     * 
     * @return string
     */
    public function render(): string;
}