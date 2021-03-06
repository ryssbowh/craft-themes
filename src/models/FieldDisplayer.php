<?php 

namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\interfaces\FieldDisplayerInterface;
use craft\base\Model;

abstract class FieldDisplayer extends Model implements FieldDisplayerInterface
{
    public $field;

    public $isDefault = false;

    public $hasOptions = false;

    public $name;

    public $handle;

    public function isDefault(): bool
    {
        return false;
    }

    public function getOptions(): Model
    {
        if (!$this->hasOptions) {
            throw FieldDisplayerException::noOptions(get_called_class());
        }
        $model = $this->getOptionsModel();
        if ($this->field) {
            $options = json_decode($this->field->options, true);
            $model->setAttributes($options, false);
        }
        return $model;
    }

    public function fields()
    {
        return ['name', 'handle', 'isDefault', 'options', 'hasOptions'];
    }

    public function getOptionsHtml(): string
    {
        return '';
    }
}