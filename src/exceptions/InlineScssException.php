<?php
namespace Ryssbowh\CraftThemes\exceptions;

class InlineScssException extends \Exception
{
    public static function noFile(string $file, string $template)
    {
        return new static("The scss file $file could not be found in $template");
    }
}