<?php 

namespace Ryssbowh\CraftThemes\exceptions;

use Ryssbowh\CraftThemes\interfaces\LayoutInterface;

class LayoutException extends \Exception
{
    public static function noTheme(string $class)
    {
        return new static("Can't get layout's theme, it's not defined.");
    }

    public static function onSave()
    {
        return new static("Layout couldn't be saved");
    }

    public static function unknownType(string $type)
    {
        return new static("Type $type is not a valid layout type");
    }

    public static function noViewMode(string $viewMode)
    {
        return new static("View mode '$viewMode' is not defined on this layout");
    }

    public static function noId(int $id)
    {
        return new static("Layout with id $id couldn't be found");
    }

    public static function defaultUndeletable()
    {
        return new static("Default layout can't be deleted");
    }

    public static function notLoaded(LayoutInterface $layout, string $method)
    {
        return new static("$method can't be called unless the layout (".get_class($layout).") is loaded");
    }

    public static function noRegion(string $region)
    {
        return new static("Region $handle doesn't exist in this layout");
    }
}