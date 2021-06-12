<?php 

namespace Ryssbowh\CraftThemes\models\blocks;

use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\models\BlockOptions;
use Ryssbowh\CraftThemes\models\blockOptions\BlockCurrentUserOptions;

class CurrentUserBlock extends Block
{
    /**
     * @var string
     */
    public $name = 'Current user';

    /**
     * @var string
     */
    public $smallDescription = 'Displays the current user';

    /**
     * @var string
     */
    public static $handle = 'current-user';

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): BlockOptions
    {
        return new BlockCurrentUserOptions;
    }
}
