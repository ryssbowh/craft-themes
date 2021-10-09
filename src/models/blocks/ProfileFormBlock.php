<?php
namespace Ryssbowh\CraftThemes\models\blocks;

use Ryssbowh\CraftThemes\models\Block;

class ProfileFormBlock extends Block
{
    /**
     * @var string
     */
    public static $handle = 'profile';

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Profile');
    }

    /**
     * @inheritDoc
     */
    public function getSmallDescription(): string
    {
        return \Craft::t('themes', 'Displays the profile form');
    }

    /**
     * @inheritDoc
     */
    public function isVisible(): bool
    {
        if (!\Craft::$app->user->getIdentity()) {
            return false;
        }
        return $this->active;
    }
}
