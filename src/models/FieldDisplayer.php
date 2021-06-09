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
    /**
     * @var boolean
     */
    public static $isDefault = false;

    /**
     * @var boolean
     */
    public $hasOptions = false;

    /**
     * @var Field
     */
    protected $_field;

    /**
     * @var Model
     */
    protected $_options;

    /**
     * @inheritDoc
     */
    public static function getHandle(): string 
    {
        return static::$handle;
    }

    /**
     * Does this displayer have options
     * 
     * @return bool
     */
    public function getHasOptions(): bool
    {
        return $this->hasOptions;
    }

    /**
     * TO BE REVIEWED
     */
    public function setField($field)
    {
        $this->_field = $field;
    }

    /**
     * TO BE REVIEWED
     */
    public function getField(): ?object
    {
        return $this->_field;
    }

    /**
     * @inheritDoc
     */
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

    /**
     * TO BE REVIEWED
     */
    public function getTheme()
    {
        return $this->field->layout->theme;
    }

    /**
     * @inheritDoc
     */
    public function eagerLoad(): array
    {
        if ($this->field instanceof CraftField and $this->field->craftField instanceof BaseRelationField) {
            return [$this->field->craftField->handle];
        }
        return [];
    }

    /**
     * @inheritDoc
     */
    public function fields()
    {
        return array_merge(parent::fields(), ['name', 'options', 'handle']);
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): Model
    {
        return new NoOptions;
    }
}