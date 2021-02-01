<?php 

namespace Ryssbowh\CraftThemes\interfaces;

interface BlockProviderInterface 
{
	/**
	 * Provider name
	 * 
	 * @return string
	 */
	public static function getName(): string;

	/**
	 * Provider handle
	 * 
	 * @return string
	 */
	public static function getHandle(): string;

	public function addBlock($block): BlockProviderInterface;

	public function getBlocks(): array;
}