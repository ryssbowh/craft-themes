<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\EntrySlickOptions;
use Ryssbowh\CraftThemes\traits\SlickRenderer;

/**
 * Renders an entry field as a slick carousel
 */
class EntrySlick extends EntryRendered
{
    use SlickRenderer;
    
    /**
     * @inheritDoc
     */
    public static $handle = 'entry-slick';

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Slick Carousel');
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return EntrySlickOptions::class;
    }
}