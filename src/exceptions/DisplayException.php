<?php 

namespace Ryssbowh\CraftThemes\exceptions;

class DisplayException extends \Exception
{
    public static function typeInvalid(string $type, array $types)
    {
        return new static("Invalid display type $type, valid values are : ".implode(', ', $types));
    }
}