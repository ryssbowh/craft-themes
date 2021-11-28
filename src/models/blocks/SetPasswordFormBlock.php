<?php
namespace Ryssbowh\CraftThemes\models\blocks;

use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\models\blockOptions\SetPasswordFormBlockOptions;

/**
 * Block displaying the set password form
 */
class SetPasswordFormBlock extends Block
{
    /**
     * @var string
     */
    public static $handle = 'set-password';

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Set password');
    }

    /**
     * @inheritDoc
     */
    public function getSmallDescription(): string
    {
        return \Craft::t('themes', 'Displays the set password form');
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return SetPasswordFormBlockOptions::class;
    }
}
