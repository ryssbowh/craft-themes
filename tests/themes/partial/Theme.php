<?php
namespace Ryssbowh\CraftThemesTests\themes\partial;

use Ryssbowh\CraftThemes\base\ThemePlugin;

class Theme extends ThemePlugin
{
    public function isPartial(): bool
    {
        return true;
    }
}