<?php
namespace Ryssbowh\CraftThemes\exceptions;

class ScssBundleException extends \Exception
{
    public static function noTheme(string $class)
    {
        return new static("$class should be instanciated with a theme parameter");
    }
}