<?php
namespace Ryssbowh\CraftThemesTests\themes\child\models\blocks;

use Ryssbowh\CraftThemes\models\Block;

class MainMenu extends Block
{
    /**
     * @var string
     */
    public static $handle = 'main-menu';

    public function getSmallDescription(): string
    {
        return '';
    }

    public function getName(): string
    {
        return '';
    }
}