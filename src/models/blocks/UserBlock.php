<?php 

namespace Ryssbowh\CraftThemes\models\blocks;

use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\models\blockOptions\BlockUserOptions;
use craft\base\Model;

class UserBlock extends Block
{
    /**
     * @var string
     */
    public $name = 'User';

    /**
     * @var string
     */
    public $smallDescription = 'Displays a user';

    /**
     * @var string
     */
    public $longDescription = 'Choose a user and a view mode to display';

    /**
     * @var string
     */
    public static $handle = 'user';

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): Model
    {
        return new BlockUserOptions;
    }
}
