<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\MatrixSlickOptions;
use Ryssbowh\CraftThemes\traits\SlickRenderer;

/**
 * Renders a matrix field as a slick carousel
 */
class MatrixSlick extends MatrixDefault
{
    use SlickRenderer;
    
    /**
     * @inheritDoc
     */
    public static $handle = 'matrix_slick';

    /**
     * @inheritDoc
     */
    public static $isDefault = false;

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
        return MatrixSlickOptions::class;
    }
}