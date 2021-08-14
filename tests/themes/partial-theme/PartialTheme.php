<?php 

namespace Ryssbowh\tests\themes\partial;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\models\ThemePlugin;
use yii\base\Event;

class PartialTheme extends ThemePlugin
{
    public function isPartial(): bool
    {
        return true;
    }
}