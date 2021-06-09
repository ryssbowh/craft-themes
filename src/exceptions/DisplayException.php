<?php 

namespace Ryssbowh\CraftThemes\exceptions;

use Ryssbowh\CraftThemes\interfaces\DisplayInterface;
use Ryssbowh\CraftThemes\models\fields\CraftField;

class DisplayException extends \Exception
{
    public static function noCraftField(CraftField $field)
    {
        return new static("Craft field is not defined on field ".get_class($field));
    }

    public static function onSave(DisplayInterface $display)
    {
        $count = static::countErrors($display->getErrors()) + static::countErrors($display->item->getErrors());
        return new static("Unable to save display, " . $count . " error(s) found : ".print_r($display->getErrors(), true) . ' ' . print_r($display->item->getErrors(), true));
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