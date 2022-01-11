<?php
namespace Ryssbowh\CraftThemes\exceptions;

class ScssCompilerException extends \Exception
{
    public static function fileNotFound(string $file)
    {
        return new static("File $file does not exist");
    }

    public static function sourcemapInvalid(array $valid)
    {
        return new static("Sourcemaps option is invalid, valid values are " . implode(', ', $valid));
    }
}