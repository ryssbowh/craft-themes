<?php 

namespace Ryssbowh\CraftThemes\blockProviders;

use Ryssbowh\CraftThemes\blocks\ContentBlock;
use Ryssbowh\CraftThemes\blocks\TemplateBlock;
use Ryssbowh\CraftThemes\blocks\TwigBlock;

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