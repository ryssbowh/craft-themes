<?php
namespace Ryssbowh\CraftThemes\exceptions;

class CreatorException extends \Exception
{
    public static function folderExists(string $folder)
    {
        return new static("The folder $folder already exists", 10);
    }

    public static function fileExists(string $file)
    {
        return new static("The file $file already exists", 11);
    }

    public static function handleInvalid(string $handle)
    {
        return new static("The handle $handle is invalid", 12);
    }

    public static function pluginDefined(string $handle)
    {
        return new static("The handle $handle is already a defined plugin", 13);
    }

    public static function classnameInvalid(string $className)
    {
        return new static("The class name $className is invalid", 14);
    }

    public static function themeUndefined(string $handle)
    {
        return new static("The theme $handle is not defined", 15);
    }
}