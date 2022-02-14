<?php
namespace Ryssbowh\CraftThemes\models\blocks;

use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\models\blockOptions\SearchFormBlockOptions;

/**
 * Block displaying the search form
 */
class SearchFormBlock extends Block
{
    /**
     * @var string
     */
    public static $handle = 'search';

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Search');
    }

    /**
     * @inheritDoc
     */
    public function getSmallDescription(): string
    {
        return \Craft::t('themes', 'Displays the search form');
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return SearchFormBlockOptions::class;
    }
}