<?php 

namespace Ryssbowh\CraftThemes\models;

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

class SystemBlockProvider extends BlockProvider
{
    /**
     * @var array
     */
    protected $_definedBlocks = [
        TemplateBlock::class,
        ContentBlock::class,
        TwigBlock::class,
        EntryBlock::class,
        CategoryBlock::class,
        UserBlock::class,
        CurrentUserBlock::class,
        GlobalBlock::class,
        SiteNameBlock::class,
        FlashMessagesBlock::class
    ];

    public function getName(): string
    {
        return \Craft::t('themes', 'System');
    }

    public function getHandle(): string
    {
        return 'system';
    }
}