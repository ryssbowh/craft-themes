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
     * @inheritDoc
     */
    public function getHasOptions(): bool
    {
        return $this->getOptionsModel() != NoOptions::class;
    }

    /**
     * TO BE REVIEWED
     */
    public function setField($field)
    {
        $this->_field = $field;
        $this->_options = null;
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
            $class = $this->getOptionsModel();
            $this->_options = new $class([
                'displayer' => $this
            ]);
        }
        return $this->_options;
    }

    /**
     * @inheritDoc
     */
    public function setOptions(array $options)
    {
        $attributes = $this->safeAttributes();
        $options = array_intersect_key($options, array_flip($attributes));
        $this->options->setAttributes($options);
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
        return array_merge(parent::fields(), ['name', 'options', 'handle', 'hasOptions']);
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return NoOptions::class;
    }
}