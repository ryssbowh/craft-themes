<?php 

namespace Ryssbowh\CraftThemes\exceptions;

use Ryssbowh\CraftThemes\interfaces\FieldDisplayerInterface;

class FieldDisplayerException extends \Exception
{
    public static function displayerNotDefined(string $handle)
    {
        return new static("$handle is not a registered field displayer handle");
    }

    public static function alreadyDefined(string $class)
    {
        return new static($class::$handle . " is already a registered field displayer");
    }
}