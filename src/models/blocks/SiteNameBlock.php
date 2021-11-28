<?php
namespace Ryssbowh\CraftThemes\models\blocks;

use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\models\blockOptions\BlockSiteNameOptions;

/**
 * Block displaying the site name
 */
class SiteNameBlock extends Block
{
    /**
     * @var string
     */
    public static $handle = 'sitename';

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Site name');
    }

    /**
     * @inheritDoc
     */
    public function getSmallDescription(): string
    {
        return \Craft::t('themes', 'Displays the site name');
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return BlockSiteNameOptions::class;
    }
}
