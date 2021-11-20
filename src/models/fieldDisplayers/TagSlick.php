<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\TagSlickOptions;
use Ryssbowh\CraftThemes\traits\SlickRenderer;

/**
 * Renders a tag field as a slick carousel
 */
class TagSlick extends TagRendered
{
    use SlickRenderer;
    
    /**
     * @inheritDoc
     */
    public static $handle = 'tag_slick';

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
    public function getOptionsModel(): string
    {
        return TagSlickOptions::class;
    }
}