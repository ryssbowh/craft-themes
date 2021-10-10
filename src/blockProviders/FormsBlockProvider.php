<?php
namespace Ryssbowh\CraftThemes\blockProviders;

use Ryssbowh\CraftThemes\base\BlockProvider;
use Ryssbowh\CraftThemes\models\blocks\LoginFormBlock;
use Ryssbowh\CraftThemes\models\blocks\ProfileFormBlock;
use Ryssbowh\CraftThemes\models\blocks\RegisterFormBlock;
use Ryssbowh\CraftThemes\models\blocks\ResetPasswordFormBlock;
use Ryssbowh\CraftThemes\models\blocks\SearchFormBlock;
use Ryssbowh\CraftThemes\models\blocks\SetPasswordFormBlock;

/**
 * Provides forms blocks
 */
class FormsBlockProvider extends BlockProvider
{
    /**
     * @var array
     */
    protected $_definedBlocks = [
        LoginFormBlock::class,
        RegisterFormBlock::class,
        ProfileFormBlock::class,
        ResetPasswordFormBlock::class,
        SetPasswordFormBlock::class,
        SearchFormBlock::class
    ];

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Forms');
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'forms';
    }
}