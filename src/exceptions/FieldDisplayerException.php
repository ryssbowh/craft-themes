<?php 

namespace Ryssbowh\CraftThemes\exceptions;

use Ryssbowh\CraftThemes\interfaces\FieldDisplayerInterface;

class FieldDisplayerException extends \Exception
{
    public static function notDefined(string $handle)
    {
        return new static("$handle is not a registered field displayer handle");
    }

    public static function alreadyDefined(string $class)
    {
        return new static($class::$handle . " is already a registered field displayer");
    }

    public static function notValid(string $displayerHandle, string $fieldClass)
    {
        return new static("$displayerHandle is not a valid displayer for $fieldClass");
    }
}