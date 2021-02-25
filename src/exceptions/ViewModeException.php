<?php 

namespace Ryssbowh\CraftThemes\exceptions;

class ViewModeException extends \Exception
{
    public static function defaultUndeletable()
    {
        return new static("Default view mode can't be deleted");
    }
}