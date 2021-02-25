<?php 

namespace Ryssbowh\CraftThemes\exceptions;

class LayoutException extends \Exception
{
    public static function noTheme(string $class)
    {
        return new static("Can't get layout's theme, it's not defined.");
    }

    public static function noType()
    {
        return new static("Layout can't be built, no type is defined for it");
    }

    public static function noElement()
    {
        return new static("Layout can't be built, no element is defined for it");
    }

    public static function unknownType(string $type)
    {
        return new static("Type $type is not a valid layout type");
    }

    public static function noId(int $id)
    {
        return new static("Layout with id $id couldn't be found");
    }

    public static function defaultUndeletable()
    {
        return new static("Default layout can't be deleted");
    }
}