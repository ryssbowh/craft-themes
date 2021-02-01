<?php 

namespace Ryssbowh\CraftThemes\interfaces;

interface BlockInterface 
{
	/**
	 * Block name
	 * 
	 * @return string
	 */
	public function getName(): string;

	/**
	 * Block handle
	 * 
	 * @return string
	 */
	public function getHandle(): string;
}