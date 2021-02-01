<?php 

namespace Ryssbowh\CraftThemes\exceptions;

use Ryssbowh\CraftThemes\interfaces\BlockInterface;

class BlockException extends \Exception
{
	public static function noClass(string $method)
	{
		return new static("A block has been passed as an array to $method but no 'class' key was found");
	}

	public static function notABlock(string $method)
	{
		return new static("Couldn't instanciate the block in ".$method.". Make sure the block implements ".BlockInterface::class);
	}
}