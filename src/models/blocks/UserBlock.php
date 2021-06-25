<?php 

namespace Ryssbowh\CraftThemes\models\blocks;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\models\BlockOptions;
use Ryssbowh\CraftThemes\models\blockOptions\BlockUserOptions;
use Ryssbowh\CraftThemes\services\LayoutService;
use craft\elements\User;

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
     * @var User
     */
    protected $_user;

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): BlockOptions
    {
        return new BlockUserOptions;
    }

    /**
     * Get layout associated to user defined in options
     * 
     * @return LayoutInterface
     */
    public function getUserLayout(): LayoutInterface
    {
        return Themes::$plugin->layouts->get($this->layout->theme, LayoutService::USER_HANDLE, '');
    }

    /**
     * Get category as defined in options
     * 
     * @return User
     */
    public function getUser(): User
    {
        if ($this->_user === null) {
            $this->_user = User::find()->uid($this->options->user)->one();
        }
        return $this->_user;
    }
}
