<?php 

namespace Ryssbowh\CraftThemes\exceptions;

use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\interfaces\ViewModeInterface;

class ViewModeException extends \Exception
{
    public static function noId(int $id)
    {
        return new static("View mode with id $id couldn't be found");
    }

    public static function noUid(string $uid)
    {
        return new static("View mode with uid $uid couldn't be found");
    }

    public static function defaultUndeletable()
    {
        return new static("Default view mode can't be deleted");
    }

    public static function defaultLayoutNoViewModes(LayoutInterface $layout)
    {
        return new static("Default layout (id $layout->id) can't have view modes");   
    }

    public static function duplicatedHandle(int $existingId, string $handle)
    {
        return new static("View mode $existingId already has the handle $handle");
    }

    public static function notAGroup(ViewModeInterface $viewMode)
    {
        return new static("Only groups can be added to view modes");
    }
}