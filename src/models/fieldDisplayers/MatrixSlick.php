<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\MatrixSlickOptions;
use Ryssbowh\CraftThemes\traits\SlickRenderer;
use craft\fields\Matrix;

/**
 * Renders a matrix field as a slick carousel
 */
class MatrixSlick extends FieldDisplayer
{
    use SlickRenderer;
    
    /**
     * @inheritDoc
     */
    public static $handle = 'matrix-slick';

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
    public static function getFieldTargets(): array
    {
        return [Matrix::class];
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return MatrixSlickOptions::class;
    }
}