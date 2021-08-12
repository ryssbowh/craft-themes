<?php 

namespace Ryssbowh\CraftThemes\interfaces;

use Ryssbowh\CraftThemes\interfaces\BlockProviderInterface;
use Ryssbowh\CraftThemes\models\BlockOptions;
use craft\base\Model;
use craft\base\Element;

interface BlockInterface
{
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
     * Get full machine name, in the form provider-handle
     * 
     * @return string
     */
    public function getMachineName(): string;

    /**
     * Model that defines this block's options 
     * 
     * @return BlockOptions
     */
    public function getOptionsModel(): BlockOptions;

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
     * Render for an element
     * 
     * @return string
     */
    public function render(Element $element): string;
}