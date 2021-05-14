<?php 

namespace Ryssbowh\CraftThemes\models\blocks;

use Ryssbowh\CraftThemes\models\Block;

class ContentBlock extends Block
{
    /**
     * @var string
     */
    public $name = 'Content';

    /**
     * @var string
     */
    public static $handle = 'content';

    /**
     * @var string
     */
    public $smallDescription = 'Displays the main page content';

    /**
     * @var string
     */
    public $longDescription = 'Should be present on each block layout';
}