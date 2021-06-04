<?php 

namespace Ryssbowh\CraftThemes\exceptions;

class FileDisplayerException extends \Exception
{
    public static function displayerNotDefined(string $handle)
    {
        return new static("$handle is not a registered file displayer handle");
    }
}