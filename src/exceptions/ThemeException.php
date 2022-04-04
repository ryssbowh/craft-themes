<?php
namespace Ryssbowh\CraftThemes\exceptions;

use Ryssbowh\CraftThemes\interfaces\ThemeInterface;

class ThemeException extends \Exception
{
    public static function notDefined(string $name)
    {
        return new static("Theme $name is not defined");
    }

    public static function wrongParameter(string $method)
    {
        return new static("\$theme parameter called in $method must be a string or a ThemeInterface instance");
    }

    public static function duplicatedRegion(string $theme, string $handle)
    {
        return new static("Theme $theme has a duplicated region handle ($handle)");
    }

    public static function noRegion(string $theme, string $region)
    {
        return new static("Theme $theme doesn't have a region $region");
    }

    public static function regionParameterMissing(string $parameter, string $handle)
    {
        return new static("Theme $handle is missing a $parameter parameter in a region definition");
    }

    public static function rootsRegistered(?ThemeInterface $theme)
    {
        $message = $theme ? "Unable to set the theme to " . $theme->handle : "Unable to unset the theme";
        return new static($message . ": You must do this earlier in the request, before the view template roots are registered");
    }

    public static function handleInvalid(ThemeInterface $theme, string $handle)
    {
        return new static("the region's handle '$handle' defined in theme ({$theme->handle}) is invalid, it can only contain alphanumeric characters and hyphens");
    }

    public static function themesNotInstalled()
    {
        return new static("The plugin Themes must be installed before installing any theme");
    }
}