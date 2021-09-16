<?php 

namespace Ryssbowh\CraftThemes\exceptions;

class GroupException extends \Exception
{
    public static function noId(int $id)
    {
        return new static("Group with id $id couldn't be found");
    }
}