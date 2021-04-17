<?php 

namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\models\blocks\ContentBlock;
use Ryssbowh\CraftThemes\models\blocks\TemplateBlock;
use Ryssbowh\CraftThemes\models\blocks\TwigBlock;

class SystemBlockProvider extends BlockProvider
{
    /**
     * @var array
     */
    protected $_definedBlocks = [
        TemplateBlock::class,
        ContentBlock::class,
        TwigBlock::class,
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