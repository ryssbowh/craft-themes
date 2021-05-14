<?php 

namespace Ryssbowh\CraftThemes\models\blocks;

use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\models\blockOptions\BlockUserOptions;
use craft\base\Model;

class FlashMessagesBlock extends Block
{
    /**
     * @var string
     */
    public $name = 'Messages';

    /**
     * @var string
     */
    public $smallDescription = 'Displays system messages';

    /**
     * @var string
     */
    public $longDescription = 'Will fetch the message from the \'notice\' and \'error\' session flash data';

    /**
     * @var string
     */
    public static $handle = 'flash-messages';
}
