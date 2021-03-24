<?php 

namespace Ryssbowh\CraftThemes\exceptions;

use Ryssbowh\CraftThemes\models\Field;

class FieldException extends \Exception
{
    public static function noId(string $id)
    {
        return new static("Field record with id '".$id."' could not be found");
    }

    public static function noDisplayer(Field $field)
    {
        return new static("Field ".get_class($field)." can't be rendered, its displayer is not set");
    }
}