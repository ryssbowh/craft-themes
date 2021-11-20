<?php
namespace Ryssbowh\CraftThemes\models\blocks;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\BlockOptionsInterface;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\models\blockOptions\BlockUserOptions;
use Ryssbowh\CraftThemes\services\LayoutService;
use craft\elements\User;

/**
 * Block displaying some users
 */
class UserBlock extends Block
{
    /**
     * @var string
     */
    public static $handle = 'user';

    /**
     * @var array
     */
    protected $_users;

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('app', 'User');
    }

    /**
     * @inheritDoc
     */
    public function getSmallDescription(): string
    {
        return \Craft::t('themes', 'Displays some users');
    }

    /**
     * @inheritDoc
     */
    public function getLongDescription(): string
    {
        return \Craft::t('themes', 'Choose one or several users and a view mode to display');
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): BlockOptionsInterface
    {
        return new BlockUserOptions;
    }

    /**
     * Get users/view modes as defined in options
     * 
     * @return array
     */
    public function getUsers(): array
    {
        if ($this->_users === null) {
            $this->_users = array_map(function ($row) {
                return [
                    'user' => User::find()->id($row['id'])->one(),
                    'viewMode' => Themes::$plugin->viewModes->getByUid($row['viewMode'])
                ];
            }, $this->options->users);
        }
        return $this->_users;
    }

    /**
     * @inheritDoc
     */
    public function beforeRender(bool $fromCache): bool
    {
        return sizeof($this->users) > 0;
    }
}
