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
    public $blocks = [
        TemplateBlock::class,
        ContentBlock::class,
        TwigBlock::class,
    ];

    /**
     * @var string
     */
    public $handle = 'system';

    /**
     * @var string
     */
    public $name = 'System';

    /**
     * Get name
     * 
     * @return string
     */
    public static function getName(): string
    {
        return \Craft::t('themes', 'System');
    }
}