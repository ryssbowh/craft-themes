<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\SuperTableSlickOptions;
use Ryssbowh\CraftThemes\traits\SlickRenderer;

/**
 * Renders a super table field as a slick carousel
 */
class SuperTableSlick extends SuperTableDefault
{
    use SlickRenderer;
    
    /**
     * @inheritDoc
     */
    public static $handle = 'super-table-slick';

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
        return SuperTableSlickOptions::class;
    }
}