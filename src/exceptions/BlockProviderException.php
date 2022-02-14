<?php
namespace Ryssbowh\CraftThemes\exceptions;

class BlockProviderException extends \Exception
{
    public static function notDefined(string $handle)
    {
        return new static("The provider with handle '$handle' doesn't exist");
    }

    public static function defined(string $handle)
    {
        return new static("The provider with handle '$handle' is already registered");
    }

    public static function blockDefined(string $handle, string $provider)
    {
        return new static("The provider '$provider' already defines a '$handle' block");
    }

    public static function noBlock(string $provider, string $handle)
    {
        return new static("The block '$handle' is not registered on provider '$provider'");
    }

    public static function handleInvalid(string $class)
    {
        return new static("$class 'handle' ({$class::$handle}) is invalid, it can only contain alphanumeric characters and hyphens");
    }
}