<?php 

namespace Ryssbowh\CraftThemes\exceptions;

use Ryssbowh\CraftThemes\models\fields\CraftField;
use Ryssbowh\CraftThemes\services\DisplayService;

class DisplayException extends \Exception
{
    public static function noCraftField(CraftField $field)
    {
        return new static("Craft field is not defined on field ".get_class($field));
    }

    public static function invalidType($type)
    {
        return new static("Type '$type' is an invalid display type. Valid types are: " . DisplayService::TYPE_FIELD . ',' . DisplayService::TYPE_GROUP);
    }
}