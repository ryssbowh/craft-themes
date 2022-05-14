<?php
namespace Ryssbowh\CraftThemes\exceptions;

class FieldException extends \Exception
{
    public static function noType()
    {
        return new static("Field can't be built, no type is defined for it");
    }

    public static function unknownType(string $type)
    {
        return new static("Field type $type is not defined");
    }

    public static function alreadyDefined(string $fieldClass)
    {
        return new static("Field $fieldClass can't be registered, its type ({$fieldClass::getType()} is already defined");
    }
}