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

    public static function installed(string $handle)
    {
        return new static("Unable to uninstall theme's data, theme $handle is still installed");
    }

    public static function notInstalled(string $handle)
    {
        return new static("Unable to install theme's data, theme $handle is not installed");
    }

    public static function wrongParameter(string $method)
    {
        return new static("\$theme parameter called in $method must be a string or a ThemeInterface instance");
    }
}