<?php 

namespace Ryssbowh\CraftThemes\exceptions;

class ViewModeException extends \Exception
{
    public static function noId(int $id)
    {
        return new static("View mode with id $id couldn't be found");
    }

    public static function noUid(string $uid)
    {
        return new static("View mode with uid $uid couldn't be found");
    }
}