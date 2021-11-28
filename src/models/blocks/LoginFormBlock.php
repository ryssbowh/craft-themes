<?php
namespace Ryssbowh\CraftThemes\models\blocks;

use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\models\blockOptions\LoginFormBlockOptions;

/**
 * Block displaying the login form
 */
class LoginFormBlock extends Block
{
    /**
     * @var string
     */
    public static $handle = 'login';

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Login');
    }

    /**
     * @inheritDoc
     */
    public function getSmallDescription(): string
    {
        return \Craft::t('themes', 'Displays the login form');
    }

    /**
     * @inheritDoc
     */
    public function beforeRender(bool $fromCache): bool
    {
        if ($this->options->onlyIfNotAuthenticated and \Craft::$app->user->getIdentity()) {
            return false;
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return LoginFormBlockOptions::class;
    }
}
