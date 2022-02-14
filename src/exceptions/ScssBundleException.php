<?php
namespace Ryssbowh\CraftThemes\exceptions;

class ScssBundleException extends \Exception
{
    public static function noTheme(string $class)
    {
        return new static("$class should be instanciated with a theme parameter");
    }

    public static function themeUndefined(string $class, string $theme)
    {
        return new static("The theme ($theme) defined in $class doesn't exist");
    }
}