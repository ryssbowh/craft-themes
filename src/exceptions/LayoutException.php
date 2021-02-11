<?php 

namespace Ryssbowh\CraftThemes\exceptions;

class LayoutException extends \Exception
{
	public static function noTheme()
	{
		return new static("Layout can't be built, no theme is defined for it");
	}
}