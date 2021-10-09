<?php
namespace Ryssbowh\CraftThemes\models\blocks;

use Ryssbowh\CraftThemes\interfaces\BlockOptionsInterface;
use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\models\blockOptions\BlockRegisterFormOptions;

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
    public function isVisible(): bool
    {
        if ($this->options->onlyIfNotAuthenticated and \Craft::$app->user->getIdentity()) {
            return false;
        }
        return $this->active;
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): BlockOptionsInterface
    {
        return new BlockRegisterFormOptions;
    }
}
