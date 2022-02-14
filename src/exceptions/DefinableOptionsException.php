<?php
namespace Ryssbowh\CraftThemes\exceptions;

class DefinableOptionsException extends \Exception
{
    public static function reserved(string $class, array $options)
    {
        return new static("The class $class defines options that are reserved words : " . implode(', ', $options));
    }
}