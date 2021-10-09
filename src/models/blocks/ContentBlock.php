<?php
namespace Ryssbowh\CraftThemes\models\blocks;

use Ryssbowh\CraftThemes\models\Block;

/**
 * Special block to handle the content of the page. 
 * It's content will be the current page layout's displays
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
}