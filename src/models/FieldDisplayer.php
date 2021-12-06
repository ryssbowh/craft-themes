<?php
namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\FieldDisplayerException;
use Ryssbowh\CraftThemes\interfaces\FieldDisplayerInterface;
use Ryssbowh\CraftThemes\interfaces\FieldInterface;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use Ryssbowh\CraftThemes\models\fields\CraftField;
use craft\base\Model;
use craft\fields\BaseRelationField;

/**
 * Base class for all field displayers
 */
abstract class FieldDisplayer extends Model implements FieldDisplayerInterface
{
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
    public static function isDefault(string $fieldClass): bool
    {
        return false;
    }

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
    public function getDescription(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function hasErrors($attribute = null)
    {
        return $this->options->hasErrors($attribute);
    }

    /**
     * @inheritDoc
     */
    public function getErrors($attribute = null)
    {
        return $this->options->getErrors($attribute);
    }

    /**
     * @inheritDoc
     */
    public function afterValidate()
    {
        $this->options->validate();
        parent::afterValidate();
    }

    /**
     * @inheritDoc
     */
    public function setField(FieldInterface $field)
    {
        $this->_field = $field;
        $this->_options = null;
    }

    /**
     * @inheritDoc
     */
    public function getField(): FieldInterface
    {
        return $this->_field;
    }

    /**
     * @inheritDoc
     */
    public function getOptions(): FieldDisplayerOptions
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
    public function getHasOptions(): bool
    {
        return sizeof($this->options->definitions) > 0;
    }

    /**
     * @inheritDoc
     */
    public function setOptions(array $options)
    {
        $this->options->setValues($options);
    }

    /**
     * @inheritDoc
     */
    public function getTheme(): ThemeInterface
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
        return array_merge(parent::fields(), ['name', 'options', 'handle', 'hasOptions', 'description']);
    }

    /**
     * @inheritDoc
     */
    public function beforeRender(&$value): bool
    {
        return !(empty($value) and Themes::$plugin->settings->hideEmptyFields);
    }
}