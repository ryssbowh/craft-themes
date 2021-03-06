<?php 

namespace Ryssbowh\CraftThemes\exceptions;

use Ryssbowh\CraftThemes\interfaces\FieldDisplayerInterface;

class FieldDisplayerException extends \Exception
{
    public static function notImplements(string $class)
    {
        return new static("$class does not implement ".FieldDisplayerInterface::class);
    }

    public static function displayerNotDefined(string $handle)
    {
        return new static("$handle is not a registered field displayer handle");
    }

    public static function noHandle(FieldDisplayerInterface $displayer)
    {
        return new static(get_class($displayer) . " must define a 'handle' parameter.");
    }

    public static function noOptions(string $class)
    {
        return new static("Can't get options model for $class, this displayer doesn't have options.");
    }
}