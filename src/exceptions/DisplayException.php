<?php 

namespace Ryssbowh\CraftThemes\exceptions;

use Ryssbowh\CraftThemes\models\Display;
use Ryssbowh\CraftThemes\models\fields\CraftField;

class DisplayException extends \Exception
{
    public static function typeInvalid(string $type, array $types)
    {
        return new static("Invalid display type $type, valid values are : ".implode(', ', $types));
    }

    public static function noCraftField(CraftField $field)
    {
        return new static("Craft field is not defined on field ".get_class($field));
    }

    public static function noType()
    {
        return new static("Field can't be built, no type is defined for it");
    }

    public static function unknownType(string $type)
    {
        return new static("Type $type is not a valid field type");
    }

    public static function noMatrixType(string $uid)
    {
        return new static("Matrix type with uid $uid is not defined");
    }

    public static function noDisplayId(int $id, string $type)
    {
        return new static(ucfirst($type) . " record for display id '".$id."' could not be found");
    }

    public static function onSave(Display $display)
    {
        $count = static::countErrors($display->getErrors()) + static::countErrors($display->item->getErrors());
        return new static("Unable to save display, " . $count . " error(s) found : ".print_r($display->getErrors(), true) . ' ' . print_r($display->item->getErrors(), true));
    }

    public static function noId(string $method)
    {
        return new static("Unable to build display, 'id' is missing in data");
    }

    public static function noItem(string $method)
    {
        return new static("Unable to build display, 'item' is missing in data");
    }

    protected static function countErrors($errors)
    {
        $count = 0;
        foreach ($errors as $error) {
            if (is_array($error)) {
                $count += static::countErrors($error);
            } else {
                $count ++;
            }
        }
        return $count;
    }
}