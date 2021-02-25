<?php 

namespace Ryssbowh\CraftThemes\exceptions;

use Ryssbowh\CraftThemes\interfaces\BlockInterface;

class BlockException extends \Exception
{
    public static function noClass(string $method)
    {
        return new static("A block has been passed as an array to $method but no 'class' key was found");
    }

    public static function notABlock(string $method)
    {
        return new static("Couldn't instanciate the block in ".$method.". Make sure the block implements ".BlockInterface::class);
    }

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

    public static function noUid(string $uid)
    {
        return new static("Block record with uid '".$uid."' could not be found");
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