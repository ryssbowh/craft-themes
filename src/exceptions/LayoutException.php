<?php
namespace Ryssbowh\CraftThemes\exceptions;

use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;

class LayoutException extends \Exception
{
    public static function noTheme(LayoutInterface $layout)
    {
        return new static("Theme is not defined on layout $layout->id");
    }

    public static function parameterMissing(string $parameter, string $method)
    {
        return new static("Layout could not be instanciated, '$parameter' parameter is missing in $method");
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

    public static function noUid(string $uid)
    {
        return new static("Layout with uid $uid couldn't be found");
    }

    public static function defaultUndeletable()
    {
        return new static("Default layout can't be deleted");
    }

    public static function notLoaded(LayoutInterface $layout, string $method)
    {
        return new static("$method can't be called unless the layout (".get_class($layout).") is loaded");
    }

    public static function alreadyExists(ThemeInterface $theme, string $type, string $uid)
    {
        $message = "Layout for theme $theme->handle and type $type already exists";
        if ($uid) {
            $message = "Layout for theme $theme->handle, type $type and element uid $uid already exists";
        }
        return new static($message);
    }

    public static function cantSave(LayoutInterface $layout)
    {
        return new static("Can't save layout of type {$layout->type}. Errors : " . json_encode($layout->getErrors()));
    }
}