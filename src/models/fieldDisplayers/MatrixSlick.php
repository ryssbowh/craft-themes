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
    public static function isDefault(string $fieldClass): bool
    {
        return false;
    }

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
        return MatrixSlickOptions::class;
    }
}