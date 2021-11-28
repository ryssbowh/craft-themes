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
    public static $handle = 'matrix_default';

    /**
     * @inheritDoc
     */
    public static $isDefault = true;

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
    public static function getFieldTarget(): String
    {
        return Matrix::class;
    }

    /**
     * @inheritDoc
     */
    public function eagerLoad(): array
    {
        $eagerLoad = [$this->field->craftField->handle];
        foreach ($this->field->getTypes() as $type) {
            foreach ($type->fields as $field) {
                if ($field->craftField instanceof BaseRelationField) {
                    $eagerLoad[] = $this->field->craftField->handle . '.' . $type->type->handle . ':' . $field->craftField->handle;
                }
            }
        }
        return $eagerLoad;
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return MatrixDefaultOptions::class;
    }
}