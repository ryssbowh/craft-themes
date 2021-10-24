<?php
namespace Ryssbowh\CraftThemesTests\themes\child;

use Ryssbowh\CraftThemes\base\ThemePlugin;

class Theme extends ThemePlugin
{
    public function getExtends(): string
    {
        return 'parent-theme';
    }
}