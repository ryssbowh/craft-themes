<?php 

namespace Ryssbowh\CraftThemes\models\blocks;

use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\models\blockOptions\BlockCurrentUserOptions;
use craft\base\Model;

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
    public function getOptionsModel(): Model
    {
        return new BlockCurrentUserOptions;
    }
}
