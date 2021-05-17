<?php 

namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\models\layouts\CategoryLayout;
use Ryssbowh\CraftThemes\models\layouts\EntryLayout;
use Ryssbowh\CraftThemes\models\layouts\Layout;

class Author extends Field
{
    public static function getType(): string
    {
        return 'author';
    }

    public static function shouldExistOnLayout(Layout $layout): bool
    {
        return ($layout instanceof EntryLayout or $layout instanceof CategoryLayout);
    }

    public function getHandle(): string
    {
        return 'author';
    }

    public function getName(): string
    {
        return \Craft::t('themes', 'Author');
    }
}