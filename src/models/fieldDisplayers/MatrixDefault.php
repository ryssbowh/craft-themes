<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use craft\fields\BaseRelationField;
use craft\fields\Matrix;

class MatrixDefault extends FieldDisplayer
{
    public $handle = 'matrix_default';

    public $isDefault = true;

    public function getName(): string
    {
        return \Craft::t('themes', 'Default');
    }

    public function getFieldTarget(): String
    {
        return Matrix::class;
    }

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
}