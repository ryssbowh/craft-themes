<?php 

namespace Ryssbowh\CraftThemes\blocks;

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
    public $smallDescription = 'Main page content';

    /**
     * @var string
     */
    public $longDescription = 'The main page content, should be present on each block layout';
}