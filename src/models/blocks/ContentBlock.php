<?php
namespace Ryssbowh\CraftThemes\models\blocks;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\models\blockOptions\ContentBlockOptions;

/**
 * Special block to handle the content of the page. 
 * Its content will be the current page layout's displays
 */
class ContentBlock extends Block
{
    /**
     * @var string
     */
    public static $handle = 'content';

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('app', 'Content');
    }

    /**
     * @inheritDoc
     */
    public function getSmallDescription(): string
    {
        return \Craft::t('themes', 'Displays the main page content');
    }

    /**
     * @inheritDoc
     */
    public function getLongDescription(): string
    {
        return \Craft::t('themes', 'Should be present on each block layout');
    }

    /**
     * @inheritDoc
     */
    public function getCanBeCached(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return ContentBlockOptions::class;
    }
}