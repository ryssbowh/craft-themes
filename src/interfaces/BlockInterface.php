<?php 

namespace Ryssbowh\CraftThemes\interfaces;

interface BlockInterface 
{
	/**
	 * Does this block define options
	 * 
	 * @return boolean
	 */
	public function hasOptions(): bool;

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
}