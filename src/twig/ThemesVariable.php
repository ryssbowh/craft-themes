<?php

namespace Ryssbowh\CraftThemes\twig;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use Ryssbowh\CraftThemes\services\LayoutService;
use Ryssbowh\CraftThemes\services\ThemesRegistry;
use Ryssbowh\CraftThemes\services\ViewModeService;

class ThemesVariable
{
    public function layouts(): LayoutService
    {
        return Themes::$plugin->layouts;
    }

    public function viewModes(): ViewModeService
    {
        return Themes::$plugin->viewModes;
    }

    public function registry(): ThemesRegistry
    {
        return Themes::$plugin->registry;
    }

    public function current(): ?ThemeInterface
    {
        return $this->registry()->getCurrent();
    }
}