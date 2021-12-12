<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\CategorySlickOptions;
use Ryssbowh\CraftThemes\traits\SlickRenderer;

/**
 * Renders an asset field as a slick carousel
 */
class CategorySlick extends CategoryRendered
{
    use SlickRenderer;
    
    /**
     * @inheritDoc
     */
    public static $handle = 'category_slick';

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
        return CategorySlickOptions::class;
    }
}