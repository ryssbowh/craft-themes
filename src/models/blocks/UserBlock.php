<?php
namespace Ryssbowh\CraftThemes\models\blocks;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\ViewModeException;
use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\models\blockOptions\UserBlockOptions;
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
     * Get users/view modes as defined in options
     * 
     * @return array
     */
    public function getUsers(): array
    {
        if ($this->_users === null) {
            $this->_users = [];
            foreach ($this->options->users as $array) {
                try {
                    $viewMode = Themes::$plugin->viewModes->getByUid($array['viewMode']);
                } catch (ViewModeException $e) {
                    continue;
                }
                $eagerLoadable = Themes::$plugin->displayerCache->getEagerLoadable($viewMode);
                $user = User::find()->id($array['id'])->with($eagerLoadable)->one();
                if ($user) {
                    $this->_users[] = [
                        'user' => $user,
                        'viewMode' => $viewMode
                    ];
                }
            }
        }
        return $this->_users;
    }

    /**
     * @inheritDoc
     */
    public function beforeRender(): bool
    {
        return sizeof($this->users) > 0;
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return UserBlockOptions::class;
    }
}
