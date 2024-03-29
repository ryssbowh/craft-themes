<?php
namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\DisplayInterface;
use Ryssbowh\CraftThemes\interfaces\FieldInterface;
use Ryssbowh\CraftThemes\interfaces\ViewModeInterface;
use Ryssbowh\CraftThemes\models\Field;
use Ryssbowh\Formidable\Models\Fields\LightSwitch;
use Twig\Markup;
use craft\base\Field as BaseField;
use craft\fields\Color;
use craft\fields\Date;
use craft\fields\Dropdown;
use craft\fields\Email;
use craft\fields\Number;
use craft\fields\PlainText;
use craft\fields\Time;
use craft\fields\Url;

/**
 * Handles a field inside a table field
 */
class TableField extends Field
{
    /**
     * @var string
     */
    public $handle;

    /**
     * @var string
     */
    public $name;

    /**
     * @var boolean
     */
    public $labelHidden = true;

    /**
     * @inheritDoc
     */
    public static function getType(): string
    {
        return 'table-field';
    }

    /**
     * @inheritDoc
     */
    public function getTargetClass(): string
    {
        return $this->craft_field_class;
    }

    /**
     * Table getter
     * 
     * @return ?Table
     */
    public function getParent(): ?FieldInterface
    {
        if ($this->_parent === null and $this->id) {
            $this->_parent = Themes::$plugin->tables->getTableForField($this->id);
        }
        return $this->_parent;
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return $this->handle;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getDisplayName(): string
    {
        return $this->craft_field_class::displayName();
    }

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            [['handle', 'name', 'craft_field_class'], 'string']
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): array
    {
        return array_merge(parent::getConfig(), [
            'handle' => $this->handle,
            'name' => $this->name,
            'craft_field_class' => $this->craft_field_class
        ]);
    }

    /**
     * @inheritDoc
     */
    public function render($value = null): Markup
    {
        return Themes::$plugin->view->renderField($this, $value);
    }

    /**
     * @inheritDoc
     */
    public static function buildConfig($column): array
    {
        switch ($column['type']) {
            case 'checkbox':
            case 'lightswitch':
                $fieldClass = LightSwitch::class;
                break;
            case 'color':
                $fieldClass = Color::class;
                break;
            case 'date':
                $fieldClass = Date::class;
                break;
            case 'select':
                $fieldClass = Dropdown::class;
                break;
            case 'email':
                $fieldClass = Email::class;
                break;
            case 'multiline':
            case 'singleline':
                $fieldClass = PlainText::class;
                break;
            case 'number':
                $fieldClass = Number::class;
                break;
            case 'time':
                $fieldClass = Time::class;
                break;
            case 'url':
                $fieldClass = Url::class;
                break;
        }

        return [
            'type' => static::getType(),
            'handle' => $column['handle'],
            'name' => $column['heading'],
            'craft_field_class' => $fieldClass,
            'displayerHandle' => Themes::$plugin->fieldDisplayers->getDefaultHandle($fieldClass) ?? ''
        ];
    }
}