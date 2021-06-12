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

    public static function noBlock(string $provider, string $handle)
    {
        return new static("The block '$handle' is not registered on provider '$provider'");
    }
}