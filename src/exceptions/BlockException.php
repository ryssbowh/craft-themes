<?php 

namespace Ryssbowh\CraftThemes\exceptions;

use Ryssbowh\CraftThemes\interfaces\BlockInterface;

class BlockException extends \Exception
{
    public static function noHandle(string $class)
    {
        return new static("Block ".$class." must have a static '\$handle' parameter");
    }

    public static function noProvider(string $class)
    {
        return new static("Block ".$class." needs to be instanciated with a '\$provider' parameter");
    }

    public static function noName(string $class)
    {
        return new static("Block ".$class." must have a '\$name' parameter");
    }

    public function noHandleInData(string $method)
    {
        return new static("Block could not be instanciated, 'handle' argument is missing in $method");  
    }

    public function noProviderInData(string $method)
    {
        return new static("Block could not be instanciated, 'provider' argument is missing in $method");    
    }
}