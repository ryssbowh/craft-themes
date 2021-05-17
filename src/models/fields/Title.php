<?php 

namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\models\layouts\CategoryLayout;
use Ryssbowh\CraftThemes\models\layouts\EntryLayout;
use Ryssbowh\CraftThemes\models\layouts\Layout;
use Ryssbowh\CraftThemes\models\layouts\VolumeLayout;
class Title extends Field
{
    public static function getType(): string
    {
        return 'title';
    }

    public static function shouldExistOnLayout(Layout $layout): bool
    {
        if ($layout instanceof EntryLayout or $layout instanceof CategoryLayout or $layout instanceof VolumeLayout) {
            return true;
        }
        return false;
    }

    public function getHandle(): string
    {
        return 'title';
    }

    public function getName(): string
    {
        return \Craft::t('themes', 'Title');
    }
}