<?php
namespace Ryssbowh\CraftThemes\models\blocks;

use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\models\blockOptions\ResetPasswordFormBlockOptions;

/**
 * Block displaying the reset password form
 */
class ResetPasswordFormBlock extends Block
{
    /**
     * @var string
     */
    public static $handle = 'reset-password';

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Reset password');
    }

    /**
     * @inheritDoc
     */
    public function getSmallDescription(): string
    {
        return \Craft::t('themes', 'Displays the reset password form');
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return ResetPasswordFormBlockOptions::class;
    }
}
