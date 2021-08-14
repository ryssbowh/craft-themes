<?php 

namespace Ryssbowh\tests\themes\child;

use Ryssbowh\CraftThemes\models\Region;
use Ryssbowh\CraftThemes\models\ThemePlugin;
use yii\base\Event;

class ChildTheme extends ThemePlugin
{
    public function getExtends(): string
    {
        return 'parent-theme';
    }
}