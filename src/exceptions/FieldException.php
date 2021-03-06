<?php 

namespace Ryssbowh\CraftThemes\exceptions;

class FieldException extends \Exception
{
    public static function noId(string $id)
    {
        return new static("Field record with id '".$id."' could not be found");
    }
}