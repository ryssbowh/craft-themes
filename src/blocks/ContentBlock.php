<?php 

namespace Ryssbowh\CraftThemes\blocks;

use Ryssbowh\CraftThemes\models\Block;

class ContentBlock extends Block
{
	public $name = 'Content';

	public static $handle = 'content';

    public $smallDescription = 'Main page content';

    public $longDescription = 'The main page content, should be present on each block layout';
}