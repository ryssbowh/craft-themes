<?php 

namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\models\layouts\Layout;
use Ryssbowh\CraftThemes\models\layouts\VolumeLayout;

class File extends Field
{
    public static function getType(): string
    {
        return 'file';
    }

    public static function shouldExistOnLayout(Layout $layout): bool
    {
        return ($layout instanceof VolumeLayout);
    }

    public function getHandle(): string
    {
        return 'file';
    }

    public function getName(): string
    {
        return \Craft::t('themes', 'File');
    }
}