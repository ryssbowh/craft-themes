<?php 

namespace Ryssbowh\CraftThemes\exceptions;

use Ryssbowh\CraftThemes\models\layouts\Layout;

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

    public static function onSave()
    {
        return new static("Layout couldn't be saved");
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

    public static function notLoaded(Layout $layout, string $method)
    {
        return new static("$method can't be called unless the layout (".get_class($layout).") is loaded");
    }

    public static function noRegion(string $region)
    {
        return new static("Region $handle doesn't exist in this layout");
    }
}