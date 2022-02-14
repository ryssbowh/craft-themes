<?php 

namespace Ryssbowh\CraftThemesTests\themes\child\models\blocks;

use Ryssbowh\CraftThemes\models\Block;

class FooterMenu extends Block
{
    /**
     * @var string
     */
    public static $handle = 'footer-menu';

    public function getSmallDescription(): string
    {
        return '';
    }

    public function getName(): string
    {
        return '';
    }
}