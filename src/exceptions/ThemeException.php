<?php 

namespace Ryssbowh\Themes\exceptions;

class ThemeException extends \Exception
{
	public static function notDefined(string $name)
	{
		return new static("Theme $name is not defined");
	}
}