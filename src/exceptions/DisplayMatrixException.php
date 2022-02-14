<?php
namespace Ryssbowh\CraftThemes\exceptions;

use Ryssbowh\CraftThemes\models\DisplayMatrixType;

class DisplayMatrixException extends \Exception
{
    public static function noTypeWithId(int $id)
    {
        return new static(DisplayMatrixType::class . " with id $id could not be found");
    }
}