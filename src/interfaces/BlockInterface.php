<?php 

namespace Ryssbowh\CraftThemes\interfaces;

use craft\base\Model;

interface BlockInterface extends RenderableInterface
{
	/**
	 * Block settings html
	 * 
	 * @return string
	 */
	public function getOptionsHtml(): string;

	/**
	 * Get full machine name
	 * 
	 * @return string
	 */
	public function getMachineName(): string;

    /**
     * Model that defines this block's options 
     * 
     * @return Model
     */
    public function getOptionsModel(): Model;
}