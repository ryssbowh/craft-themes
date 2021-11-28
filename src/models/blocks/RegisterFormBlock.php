<?php
namespace Ryssbowh\CraftThemes\models\blocks;

use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\models\blockOptions\RegisterFormBlockOptions;

/**
 * Block displaying the register form
 */
class RegisterFormBlock extends Block
{
    /**
     * @var string
     */
    public static $handle = 'register';

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Register');
    }

    /**
     * @inheritDoc
     */
    public function getSmallDescription(): string
    {
        return \Craft::t('themes', 'Displays the register form');
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
        return RegisterFormBlockOptions::class;
    }
}
