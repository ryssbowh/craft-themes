<?php 

namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\exceptions\FieldDisplayerException;
use Ryssbowh\CraftThemes\interfaces\FieldDisplayerInterface;
use Ryssbowh\CraftThemes\models\displayerOptions\NoOptions;
use craft\base\Model;
use craft\fields\BaseRelationField;

abstract class FieldDisplayer extends Model implements FieldDisplayerInterface
{
    public $field;

    public $isDefault = false;

    public $hasOptions = false;

    public $handle;

    public function getOptions(): Model
    {
        $model = $this->getOptionsModel();
        if ($this->field) {
            $model->setAttributes($this->field->options, false);
        }
        return $model;
    }

    public function eagerLoad(): array
    {
        if ($this->field->craftField instanceof BaseRelationField) {
            return [$this->field->craftField->handle];
        }
        return [];
    }

    public function fields()
    {
        return array_merge(parent::fields(), ['name', 'options']);
    }

    public function getOptionsModel(): Model
    {
        return new NoOptions;
    }

    public function getOptionsHtml(): string
    {
        return '';
    }
}