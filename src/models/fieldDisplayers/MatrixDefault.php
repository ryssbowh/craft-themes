<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\MatrixDefaultOptions;
use craft\fields\BaseRelationField;
use craft\fields\Matrix;

/**
 * Renders a matrix field
 */
class MatrixDefault extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'matrix-default';

    /**
     * @inheritDoc
     */
    public static function isDefault(string $fieldClass): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Default');
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
        return MatrixDefaultOptions::class;
    }
}