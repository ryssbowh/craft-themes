<?php 

namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\exceptions\FieldDisplayerException;
use Ryssbowh\CraftThemes\interfaces\FieldDisplayerInterface;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\NoOptions;
use Ryssbowh\CraftThemes\models\fields\CraftField;
use craft\base\Model;
use craft\fields\BaseRelationField;

abstract class FieldDisplayer extends Model implements FieldDisplayerInterface
{
    public static $isDefault = false;

    public $hasOptions = false;

    protected $_field;

    protected $_options;

    public function getHandle(): string 
    {
        return $this::$handle;
    }

    public function setField($field)
    {
        $this->_field = $field;
    }

    public function getField(): ?object
    {
        return $this->_field;
    }

    public function getOptions(): Model
    {
        if ($this->_options === null) {
            $model = $this->getOptionsModel();
            $model->displayer = $this;
            $model->setAttributes($this->field->options, false);
            $this->_options = $model;
        }
        return $this->_options;
    }

    public function getTheme()
    {
        return $this->field->layout->theme;
    }

    public function eagerLoad(): array
    {
        if ($this->field instanceof CraftField and $this->field->craftField instanceof BaseRelationField) {
            return [$this->field->craftField->handle];
        }
        return [];
    }

    public function fields()
    {
        return array_merge(parent::fields(), ['name', 'options', 'handle']);
    }

    public function getOptionsModel(): Model
    {
        return new NoOptions;
    }
}