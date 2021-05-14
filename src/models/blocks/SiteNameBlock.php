<?php 

namespace Ryssbowh\CraftThemes\models\blocks;

use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\models\blockOptions\BlockUserOptions;
use craft\base\Model;

class SiteNameBlock extends Block
{
    /**
     * @var string
     */
    public $name = 'Site name';

    /**
     * @var string
     */
    public $smallDescription = 'Displays the site name';

    /**
     * @var string
     */
    public static $handle = 'sitename';
}
