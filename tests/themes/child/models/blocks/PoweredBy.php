<?php 

namespace Ryssbowh\CraftThemesTests\themes\child\models\blocks;

use Ryssbowh\CraftThemes\models\Block;

class PoweredBy extends Block
{
    /**
     * @var string
     */
    public static $handle = 'powered-by';

    public function getSmallDescription(): string
    {
        return '';
    }

    public function getName(): string
    {
        return '';
    }
}