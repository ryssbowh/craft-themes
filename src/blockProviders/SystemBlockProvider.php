<?php
namespace Ryssbowh\CraftThemes\blockProviders;

use Ryssbowh\CraftThemes\base\BlockProvider;
use Ryssbowh\CraftThemes\models\blocks\AssetBlock;
use Ryssbowh\CraftThemes\models\blocks\CategoryBlock;
use Ryssbowh\CraftThemes\models\blocks\ContentBlock;
use Ryssbowh\CraftThemes\models\blocks\CurrentUserBlock;
use Ryssbowh\CraftThemes\models\blocks\EntryBlock;
use Ryssbowh\CraftThemes\models\blocks\FlashMessagesBlock;
use Ryssbowh\CraftThemes\models\blocks\GlobalBlock;
use Ryssbowh\CraftThemes\models\blocks\SiteNameBlock;
use Ryssbowh\CraftThemes\models\blocks\TemplateBlock;
use Ryssbowh\CraftThemes\models\blocks\TwigBlock;
use Ryssbowh\CraftThemes\models\blocks\UserBlock;

/**
 * Provides default system blocks
 */
class SystemBlockProvider extends BlockProvider
{
    /**
     * @inheritDoc
     */
    protected $_definedBlocks = [
        TemplateBlock::class,
        ContentBlock::class,
        TwigBlock::class,
        EntryBlock::class,
        CategoryBlock::class,
        AssetBlock::class,
        UserBlock::class,
        CurrentUserBlock::class,
        GlobalBlock::class,
        SiteNameBlock::class,
        FlashMessagesBlock::class
    ];

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('app', 'System');
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'system';
    }
}