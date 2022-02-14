<?php
namespace Ryssbowh\CraftThemes\models\blocks;

use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\models\blockOptions\ProfileFormBlockOptions;

/**
 * Block displaying the profile form
 */
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
    public function beforeRender(): bool
    {
        return \Craft::$app->user->getIdentity() != null;
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return ProfileFormBlockOptions::class;
    }
}
