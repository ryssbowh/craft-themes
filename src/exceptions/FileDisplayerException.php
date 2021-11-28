<?php
namespace Ryssbowh\CraftThemes\exceptions;

class FileDisplayerException extends \Exception
{
    public static function displayerNotDefined(string $handle)
    {
        return new static("$handle is not a registered file displayer handle");
    }

    public static function alreadyDefined(string $class)
    {
        return new static($class::$handle . " is already a registered file displayer");
    }

    public static function reserved(string $class, array $options)
    {
        return new static("The class $class defines options that are reserved words : " . implode(', ', $options);
    }
}