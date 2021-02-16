<?php 

namespace Ryssbowh\CraftThemes\exceptions;

class ThemeException extends \Exception
{
	public static function notDefined(string $name)
	{
		return new static("Theme $name is not defined");
	}

	public static function handleDefined(string $handle, string $class)
	{
		return new static("Theme's handle $handle is already defined by $class");
	}
}