<?php
namespace Ryssbowh\CraftThemes\models\blocks;

use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\models\BlockOptions;
use Ryssbowh\CraftThemes\models\blockOptions\BlockTemplateOptions;

/**
 * Block displaying a custom template
 */
class TemplateBlock extends Block
{
    /**
     * @var string
     */
    public static $handle = 'template';

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('app', 'Template');
    }

    /**
     * @inheritDoc
     */
    public function getSmallDescription(): string
    {
        return \Craft::t('themes', 'A custom template');
    }

    /**
     * @inheritDoc
     */
    public function getLongDescription(): string
    {
        return \Craft::t('themes', 'Define the template rendering this block in the options');
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return BlockTemplateOptions::class;
    }
}
